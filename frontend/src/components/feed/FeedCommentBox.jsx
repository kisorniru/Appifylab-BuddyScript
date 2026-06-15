import { useRef, useState } from 'react';
import { authApi } from '../../api/authApi.js';
import AssetImage from '../common/AssetImage.jsx';
import { CommentImageIcon, MicrophoneIcon } from './FeedIcons.jsx';
import { getCommentMediaThumb, getCommentMediaUrl, isAudioCommentMedia } from './feedUtils.js';

export function CommentBox({ id, inputRef = null, onSubmit = null, disabled = false }) {
  const [value, setValue] = useState('');
  const [mediaFile, setMediaFile] = useState(null);
  const [mediaType, setMediaType] = useState(null);
  const [mediaPreview, setMediaPreview] = useState('');
  const [isRecording, setIsRecording] = useState(false);
  const fileInputRef = useRef(null);
  const recorderRef = useRef(null);
  const chunksRef = useRef([]);
  const user = authApi.getStoredUser();

  async function submitComment() {
    const body = value.trim();
    if ((!body && !mediaFile) || !onSubmit || disabled) return;

    try {
      await onSubmit({ body, media: mediaFile, mediaType });
      setValue('');
      setMediaFile(null);
      setMediaType(null);
      if (mediaPreview) URL.revokeObjectURL(mediaPreview);
      setMediaPreview('');
      if (fileInputRef.current) fileInputRef.current.value = '';
    } catch {
      // Keep the typed text so the user can retry.
    }
  }

  function setCommentMedia(file, type) {
    if (!file) return;
    if (mediaPreview) URL.revokeObjectURL(mediaPreview);

    setMediaFile(file);
    setMediaType(type);
    setMediaPreview(URL.createObjectURL(file));
  }

  function clearCommentMedia() {
    if (mediaPreview) URL.revokeObjectURL(mediaPreview);
    setMediaFile(null);
    setMediaType(null);
    setMediaPreview('');
    if (fileInputRef.current) fileInputRef.current.value = '';
  }

  async function toggleVoiceRecording() {
    if (isRecording && recorderRef.current) {
      recorderRef.current.stop();
      return;
    }

    if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) return;

    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      const options = MediaRecorder.isTypeSupported('audio/webm') ? { mimeType: 'audio/webm' } : {};
      const recorder = new MediaRecorder(stream, options);
      chunksRef.current = [];
      recorderRef.current = recorder;

      recorder.ondataavailable = (event) => {
        if (event.data?.size) chunksRef.current.push(event.data);
      };
      recorder.onstop = () => {
        stream.getTracks().forEach((track) => track.stop());
        const blob = new Blob(chunksRef.current, { type: recorder.mimeType || 'audio/webm' });
        const file = new File([blob], `comment-voice-${Date.now()}.webm`, { type: blob.type });
        setCommentMedia(file, 3);
        setIsRecording(false);
      };

      recorder.start();
      setIsRecording(true);
    } catch {
      setIsRecording(false);
    }
  }

  return (
    <div className="_feed_inner_comment_box">
      <form
        className="_feed_inner_comment_box_form"
        onSubmit={(event) => {
          event.preventDefault();
          submitComment();
        }}
      >
        <input
          ref={fileInputRef}
          type="file"
          className="_bs_hidden_file"
          accept="image/*"
          onChange={(event) => setCommentMedia(event.target.files?.[0], 1)}
        />
        <div className="_feed_inner_comment_box_content">
          <div className="_feed_inner_comment_box_content_image">
            <AssetImage name={user?.avatar || 'profile.png'} alt="" className="_comment_img" />
          </div>
          <div className="_feed_inner_comment_box_content_txt">
            <textarea
              ref={inputRef}
              className="form-control _comment_textarea"
              placeholder="Write a comment"
              id={id}
              value={value}
              disabled={disabled}
              onChange={(event) => setValue(event.target.value)}
              onKeyDown={(event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                  event.preventDefault();
                  submitComment();
                }
              }}
            />
          </div>
        </div>
        {mediaFile && (
          <div className="_comment_attach_preview">
            {mediaType === 3 ? (
              <div className="_comment_voice_preview">
                <MicrophoneIcon />
                <span>{mediaFile.name}</span>
              </div>
            ) : (
              <img src={mediaPreview} alt="Selected comment attachment" />
            )}
            <button type="button" className="_comment_attach_remove" onClick={clearCommentMedia} aria-label="Remove attachment">
              x
            </button>
          </div>
        )}
        <div className="_feed_inner_comment_box_icon">
          <button type="button" className={`_feed_inner_comment_box_icon_btn${isRecording ? ' _comment_recording' : ''}`} onClick={toggleVoiceRecording} aria-label="Record voice comment"><MicrophoneIcon /></button>
          <button type="button" className="_feed_inner_comment_box_icon_btn" onClick={() => fileInputRef.current?.click()} aria-label="Attach image"><CommentImageIcon /></button>
        </div>
      </form>
    </div>
  );
}

export function CommentMediaList({ media = [] }) {
  const [previewImage, setPreviewImage] = useState(null);

  if (!media.length) return null;

  return (
    <>
      <div className="_comment_media_list">
        {media.map((item) => {
          const mediaUrl = getCommentMediaUrl(item);

          if (isAudioCommentMedia(item)) {
            return (
              <audio key={item.id || mediaUrl} className="_comment_audio" controls preload="metadata">
                <source src={mediaUrl} type={item.mimeType || item.mime_type || 'audio/webm'} />
              </audio>
            );
          }

          return (
            <button
              type="button"
              className="_comment_media_thumb"
              key={item.id || mediaUrl}
              onClick={() => setPreviewImage(mediaUrl)}
              aria-label="Preview comment image"
            >
              <img src={getCommentMediaThumb(item)} alt="Comment attachment" />
            </button>
          );
        })}
      </div>
      {previewImage && (
        <button type="button" className="_comment_image_lightbox" onClick={() => setPreviewImage(null)} aria-label="Close image preview">
          <img src={previewImage} alt="Comment attachment preview" />
        </button>
      )}
    </>
  );
}
