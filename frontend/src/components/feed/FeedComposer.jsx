import { useEffect, useRef, useState } from 'react';
import { feedApi } from '../../api/feedApi.js';
import AssetImage from '../common/AssetImage.jsx';
import { ActionIcon, PencilIcon, SendIcon, VisibilityIcon } from './FeedIcons.jsx';

export function ToastAlert({ toast, onClose }) {
  useEffect(() => {
    if (!toast) return undefined;

    const timer = window.setTimeout(onClose, 3800);
    return () => window.clearTimeout(timer);
  }, [toast, onClose]);

  if (!toast) return null;

  return (
    <div className={`_bs_toast_alert _bs_toast_alert_${toast.type}`} role="status" aria-live="polite">
      <div className="_bs_toast_alert_icon">
        {toast.type === 'success' ? (
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="10" fill="none" viewBox="0 0 13 10">
            <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M1 5l3 3 8-7" />
          </svg>
        ) : (
          '!'
        )}
      </div>
      <p className="_bs_toast_alert_message">{toast.message}</p>
      <button type="button" className="_bs_toast_alert_close" onClick={onClose} aria-label="Close alert">
        x
      </button>
      <span className="_bs_toast_alert_progress" />
    </div>
  );
}

export function Composer({ onPostCreated, onToast }) {
  const [body, setBody] = useState('');
  const [media, setMedia] = useState(null);
  const [mediaPreviewUrl, setMediaPreviewUrl] = useState('');
  const [mediaPreviewType, setMediaPreviewType] = useState('image');
  const [visibility, setVisibility] = useState(1);
  const [visibilityMenuOpen, setVisibilityMenuOpen] = useState(false);
  const [status, setStatus] = useState('idle');
  const mediaInputRef = useRef(null);
  const mediaModeRef = useRef('image');

  useEffect(() => () => {
    if (mediaPreviewUrl) URL.revokeObjectURL(mediaPreviewUrl);
  }, [mediaPreviewUrl]);

  function openMediaPicker(mode) {
    mediaModeRef.current = mode;
    if (mediaInputRef.current) {
      mediaInputRef.current.accept = mode === 'video' ? 'video/*' : 'image/*';
      mediaInputRef.current.click();
    }
  }

  function handleMediaChange(event) {
    const file = event.target.files?.[0] || null;
    if (mediaPreviewUrl) URL.revokeObjectURL(mediaPreviewUrl);

    setMedia(file);
    setMediaPreviewType(mediaModeRef.current);
    setMediaPreviewUrl(file ? URL.createObjectURL(file) : '');
  }

  function clearMediaPreview() {
    if (mediaPreviewUrl) URL.revokeObjectURL(mediaPreviewUrl);
    setMedia(null);
    setMediaPreviewUrl('');
    setMediaPreviewType('image');
    if (mediaInputRef.current) mediaInputRef.current.value = '';
  }

  async function handleSubmit(event) {
    event.preventDefault();
    const trimmedBody = body.trim();
    if (!trimmedBody) {
      setStatus('error');
      onToast?.({ type: 'error', message: 'Please write something first.' });
      return;
    }

    setStatus('submitting');

    try {
      await feedApi.createPost({
        body: trimmedBody,
        visibility,
        media,
        mediaType: media ? (mediaModeRef.current === 'video' ? 2 : 1) : undefined,
      });
      setBody('');
      clearMediaPreview();
      setStatus('success');
      onToast?.({ type: 'success', message: 'Post created successfully.' });
      onPostCreated?.();
    } catch (error) {
      setStatus('error');
      onToast?.({ type: 'error', message: error?.message || 'Post could not be created.' });
    }
  }

  return (
    <form className="_feed_inner_text_area _b_radious6 _padd_b24 _padd_t24 _padd_r24 _padd_l24 _mar_b16" onSubmit={handleSubmit}>
      <input ref={mediaInputRef} type="file" className="_bs_hidden_file" onChange={handleMediaChange} />
      <div className="_composer_visibility_menu">
        <button
          type="button"
          className="_composer_visibility_btn"
          aria-label={visibility === 1 ? 'Post visibility: Public' : 'Post visibility: Private'}
          aria-expanded={visibilityMenuOpen}
          onClick={() => setVisibilityMenuOpen((open) => !open)}
        >
          <VisibilityIcon visibility={visibility} />
        </button>
        {visibilityMenuOpen && (
          <div className="_composer_visibility_dropdown">
            {[{ value: 1, label: 'Public' }, { value: 2, label: 'Private' }].map((item) => (
              <button
                type="button"
                className={`_composer_visibility_option${visibility === item.value ? ' _composer_visibility_option_active' : ''}`}
                key={item.value}
                onClick={() => {
                  setVisibility(item.value);
                  setVisibilityMenuOpen(false);
                }}
              >
                <span><VisibilityIcon visibility={item.value} size={18} /></span>
                {item.label}
              </button>
            ))}
          </div>
        )}
      </div>
      <div className="_feed_inner_text_area_box">
        <div className="_feed_inner_text_area_box_image">
          <AssetImage name="txt_img.png" alt="User" className="_txt_img" />
        </div>
        <div className="form-floating _feed_inner_text_area_box_form">
          <textarea
            className="form-control _textarea"
            placeholder="Leave a comment here"
            id="floatingTextarea"
            value={body}
            onChange={(event) => setBody(event.target.value)}
          />
          <label className="_feed_textarea_label" htmlFor="floatingTextarea">
            Write something ...
            <PencilIcon />
          </label>
        </div>
      </div>
      {media && mediaPreviewUrl && (
        <div className="_composer_media_preview">
          <div className="_composer_media_preview_inner">
            {mediaPreviewType === 'video' ? (
              <video className="_composer_media_video" controls preload="metadata">
                <source src={mediaPreviewUrl} type={media.type || 'video/mp4'} />
              </video>
            ) : (
              <img className="_composer_media_img" src={mediaPreviewUrl} alt="Selected post attachment preview" />
            )}
            <button type="button" className="_composer_media_remove" onClick={clearMediaPreview} aria-label="Remove selected media">
              x
            </button>
          </div>
        </div>
      )}
      <div className="_feed_inner_text_area_bottom">
        <div className="_feed_inner_text_area_item">
          {['Photo', 'Video', 'Event', 'Article'].map((item) => (
            <div className={`_feed_inner_text_area_bottom_${item.toLowerCase()} _feed_common`} key={item}>
              <button
                type="button"
                className="_feed_inner_text_area_bottom_photo_link"
                onClick={() => {
                  if (item === 'Photo') openMediaPicker('image');
                  if (item === 'Video') openMediaPicker('video');
                }}
              >
                <span className="_feed_inner_text_area_bottom_photo_iamge _mar_img"><ActionIcon type={item} /></span>
                {item}
              </button>
            </div>
          ))}
        </div>
        <div className="_feed_inner_text_area_btn">
          <button type="submit" className="_feed_inner_text_area_btn_link" disabled={status === 'submitting'}>
            <SendIcon />
            <span>{status === 'submitting' ? 'Posting...' : 'Post'}</span>
          </button>
        </div>
      </div>
      <div className="_feed_inner_text_area_bottom_mobile">
        <div className="_feed_inner_text_mobile">
          <div className="_feed_inner_text_area_item">
            {['Photo', 'Video', 'Event', 'Article'].map((item) => (
              <div className={`_feed_inner_text_area_bottom_${item.toLowerCase()} _feed_common`} key={item}>
                <button
                  type="button"
                  className="_feed_inner_text_area_bottom_photo_link"
                  aria-label={item}
                  onClick={() => {
                    if (item === 'Photo') openMediaPicker('image');
                    if (item === 'Video') openMediaPicker('video');
                  }}
                >
                  <span className="_feed_inner_text_area_bottom_photo_iamge _mar_img"><ActionIcon type={item} /></span>
                </button>
              </div>
            ))}
          </div>
          <div className="_feed_inner_text_area_btn">
            <button type="submit" className="_feed_inner_text_area_btn_link" disabled={status === 'submitting'}>
              <SendIcon />
              <span>{status === 'submitting' ? 'Posting...' : 'Post'}</span>
            </button>
          </div>
        </div>
      </div>
    </form>
  );
}
