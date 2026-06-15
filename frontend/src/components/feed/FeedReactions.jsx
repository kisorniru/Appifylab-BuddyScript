import { useCallback, useEffect, useRef, useState } from 'react';
import AssetImage from '../common/AssetImage.jsx';
import { CommentActionIcon, getReactionDisplay, ReactionPicker, ShareActionIcon } from './FeedIcons.jsx';
import { formatFeedTime, normalizeReactionUser } from './feedUtils.js';

export function ReactionRow({ activeReaction = null, onReact = null, onCommentClick = null, disabled = false }) {
  const [pickerOpen, setPickerOpen] = useState(false);
  const closeTimerRef = useRef(null);
  const reactionDisplay = getReactionDisplay(activeReaction);
  const hasReaction = [1, 2].includes(Number(activeReaction));

  function openPicker() {
    if (closeTimerRef.current) clearTimeout(closeTimerRef.current);
    setPickerOpen(true);
  }

  function scheduleClosePicker() {
    if (closeTimerRef.current) clearTimeout(closeTimerRef.current);
    closeTimerRef.current = setTimeout(() => setPickerOpen(false), 180);
  }

  return (
    <div className="_feed_inner_timeline_reaction">
      <div
        className="_reaction_picker_wrap _feed_reaction_wrap"
        onMouseEnter={openPicker}
        onMouseLeave={scheduleClosePicker}
      >
        <button
          type="button"
          className={`_feed_inner_timeline_reaction_emoji _feed_reaction${hasReaction ? ' _feed_reaction_active' : ''} ${reactionDisplay.className}`}
          onClick={() => setPickerOpen((open) => !open)}
          disabled={disabled}
        >
          <span className="_feed_inner_timeline_reaction_link"><span>{reactionDisplay.icon}{reactionDisplay.label}</span></span>
        </button>
        {pickerOpen && (
          <ReactionPicker
            activeReaction={activeReaction}
            disabled={disabled}
            onReact={(reactionType) => {
              setPickerOpen(false);
              if (onReact) onReact(reactionType);
            }}
          />
        )}
      </div>
      <button type="button" className="_feed_inner_timeline_reaction_comment _feed_reaction" onClick={onCommentClick}>
        <span className="_feed_inner_timeline_reaction_link"><span><CommentActionIcon />Comment</span></span>
      </button>
      <button type="button" className="_feed_inner_timeline_reaction_share _feed_reaction">
        <span className="_feed_inner_timeline_reaction_link"><span><ShareActionIcon />Share</span></span>
      </button>
    </div>
  );
}

export function ReactorsModal({ open, title = 'Reactions', onClose, loadItems }) {
  const [items, setItems] = useState([]);
  const [meta, setMeta] = useState({ has_more: false, next_cursor: null });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const loadReactors = useCallback(async (cursor = null) => {
    if (!loadItems) return;

    setLoading(true);
    setError('');

    try {
      const result = await loadItems({ cursor, limit: 10 });
      const nextItems = (result.items || []).map(normalizeReactionUser);
      setItems((current) => (cursor ? [...current, ...nextItems] : nextItems));
      setMeta(result.meta || {});
    } catch (loadError) {
      setError(loadError.message || 'Unable to load reactions.');
    } finally {
      setLoading(false);
    }
  }, [loadItems]);

  useEffect(() => {
    if (!open) return;
    setItems([]);
    setMeta({ has_more: false, next_cursor: null });
    loadReactors();
  }, [open, loadReactors]);

  if (!open) return null;

  return (
    <div className="_reactors_modal_backdrop" role="presentation" onMouseDown={onClose}>
      <div className="_reactors_modal" role="dialog" aria-modal="true" aria-label="Post reactions" onMouseDown={(event) => event.stopPropagation()}>
        <div className="_reactors_modal_head">
          <h4>{title}</h4>
          <button type="button" className="_reactors_modal_close" onClick={onClose} aria-label="Close reactions popup">
            x
          </button>
        </div>
        <div className="_reactors_modal_body">
          {items.map((item) => {
            const reactionDisplay = getReactionDisplay(item.reactionType);

            return (
              <div className="_reactors_modal_item" key={item.id}>
                <AssetImage name={item.userAvatar} alt="" className="_reactors_modal_avatar" />
                <div className="_reactors_modal_text">
                  <h5>{item.userName}</h5>
                  <p>{reactionDisplay.label} . {formatFeedTime(item.reactedAt)}</p>
                </div>
                <span className={`_reactors_modal_reaction ${reactionDisplay.className}`}>
                  {reactionDisplay.icon}
                </span>
              </div>
            );
          })}
          {loading && <div className="_reactors_modal_state">Loading reactions...</div>}
          {error && <div className="_reactors_modal_state _reactors_modal_error">{error}</div>}
          {!loading && !error && !items.length && <div className="_reactors_modal_state">No reactions found.</div>}
        </div>
        {meta.has_more && (
          <button type="button" className="_reactors_modal_more" onClick={() => loadReactors(meta.next_cursor)} disabled={loading}>
            {loading ? 'Loading...' : 'View more'}
          </button>
        )}
      </div>
    </div>
  );
}
