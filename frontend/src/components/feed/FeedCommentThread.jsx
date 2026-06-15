import { useEffect, useRef, useState } from 'react';
import { feedApi } from '../../api/feedApi.js';
import AssetImage from '../common/AssetImage.jsx';
import { getReactionDisplay, HeartIcon, ReactionPicker, ThumbIcon } from './FeedIcons.jsx';
import { formatCompactFeedTime, normalizeComment } from './feedUtils.js';
import { CommentBox, CommentMediaList } from './FeedCommentBox.jsx';
import { ReactorsModal } from './FeedReactions.jsx';

export function CommentThreadItem({ comment, postId, onReplyCreated = null, depth = 0 }) {
  const [currentComment, setCurrentComment] = useState(comment);
  const [replies, setReplies] = useState([]);
  const [repliesMeta, setRepliesMeta] = useState({ has_more: false, next_cursor: null });
  const [repliesOpen, setRepliesOpen] = useState(depth > 0);
  const [loadingReplies, setLoadingReplies] = useState(false);
  const [replySubmitting, setReplySubmitting] = useState(false);
  const [reactionSubmitting, setReactionSubmitting] = useState(false);
  const [reactionPickerOpen, setReactionPickerOpen] = useState(false);
  const [reactorsOpen, setReactorsOpen] = useState(false);
  const reactionCloseTimerRef = useRef(null);
  const [error, setError] = useState('');
  const hasViewerReaction = [1, 2].includes(Number(currentComment.viewerReaction));
  const commentReactionDisplay = getReactionDisplay(currentComment.viewerReaction);

  useEffect(() => {
    setCurrentComment(comment);
  }, [comment]);

  async function loadReplies(cursor = null) {
    if (!currentComment.id || loadingReplies) return;

    setLoadingReplies(true);
    setError('');

    try {
      const result = await feedApi.getPostComments({
        postId,
        parentId: currentComment.id,
        cursor,
        limit: 5,
      });
      const nextReplies = (result.items || []).map(normalizeComment);
      setReplies((items) => (cursor ? [...items, ...nextReplies] : nextReplies));
      setRepliesMeta(result.meta || {});
      setRepliesOpen(true);
    } catch (loadError) {
      setError(loadError.message || 'Unable to load replies.');
    } finally {
      setLoadingReplies(false);
    }
  }

  async function createReply(payload) {
    setReplySubmitting(true);
    setError('');

    try {
      const reply = await feedApi.createPostComment({
        postId,
        parentId: currentComment.id,
        body: payload.body,
        media: payload.media,
        mediaType: payload.mediaType,
      });
      const normalizedReply = normalizeComment(reply);
      setReplies((items) => [normalizedReply, ...items]);
      setRepliesOpen(true);
      setCurrentComment((item) => ({ ...item, replyCount: item.replyCount + 1 }));
      if (onReplyCreated) onReplyCreated(normalizedReply);
    } catch (replyError) {
      setError(replyError.message || 'Unable to post reply.');
      throw replyError;
    } finally {
      setReplySubmitting(false);
    }
  }

  async function handleCommentReaction(reactionType) {
    if (reactionSubmitting) return;

    setReactionSubmitting(true);
    setError('');

    try {
      const updatedComment = await feedApi.toggleCommentReaction({ commentId: currentComment.id, reactionType });
      setCurrentComment(normalizeComment(updatedComment));
    } catch (reactionError) {
      setError(reactionError.message || 'Unable to update reaction.');
    } finally {
      setReactionSubmitting(false);
    }
  }

  function openCommentReactionPicker() {
    if (reactionCloseTimerRef.current) clearTimeout(reactionCloseTimerRef.current);
    setReactionPickerOpen(true);
  }

  function scheduleCloseCommentReactionPicker() {
    if (reactionCloseTimerRef.current) clearTimeout(reactionCloseTimerRef.current);
    reactionCloseTimerRef.current = setTimeout(() => setReactionPickerOpen(false), 180);
  }

  return (
    <div className={`_comment_thread_item${depth > 0 ? ' _comment_thread_reply' : ''}`}>
      <ReactorsModal
        open={reactorsOpen}
        title="Comment reactions"
        onClose={() => setReactorsOpen(false)}
        loadItems={({ cursor, limit }) => feedApi.getCommentReactionUsers({ commentId: currentComment.id, cursor, limit })}
      />
      <div className="_comment_main">
        <div className="_comment_image">
          <a href="#0" className="_comment_image_link">
            <AssetImage name={currentComment.authorAvatar} alt="" className="_comment_img1" />
          </a>
        </div>
        <div className="_comment_area">
          <div className="_comment_details">
            <div className="_comment_details_top">
              <div className="_comment_name">
                <a href="#0"><h4 className="_comment_name_title">{currentComment.authorName}</h4></a>
              </div>
            </div>
            {currentComment.body && (
              <div className="_comment_status">
                <p className="_comment_status_text"><span>{currentComment.body}</span></p>
              </div>
            )}
            <CommentMediaList media={currentComment.media} />
            {currentComment.reactionCount > 0 && (
              <button type="button" className="_total_reactions _comment_reactors_trigger" onClick={() => setReactorsOpen(true)} aria-label="View comment reactions">
                <div className="_total_react">
                  <span className="_reaction_like"><ThumbIcon size={16} /></span>
                  <span className="_reaction_heart"><HeartIcon size={16} /></span>
                </div>
                <span className="_total">{currentComment.reactionCount}</span>
              </button>
            )}
          </div>
          <div className="_comment_reply">
            <div className="_comment_reply_num">
              <ul className="_comment_reply_list">
                <li>
                  <span
                    className="_reaction_picker_wrap _comment_reaction_picker_wrap"
                    onMouseEnter={openCommentReactionPicker}
                    onMouseLeave={scheduleCloseCommentReactionPicker}
                  >
                    <button
                      type="button"
                      className={`_comment_reply_btn${hasViewerReaction ? ' _comment_reply_btn_active' : ''} ${commentReactionDisplay.className}`}
                      onClick={() => setReactionPickerOpen((open) => !open)}
                      disabled={reactionSubmitting}
                    >
                      {commentReactionDisplay.label}.
                    </button>
                    {reactionPickerOpen && (
                      <ReactionPicker
                        activeReaction={currentComment.viewerReaction}
                        compact
                        disabled={reactionSubmitting}
                        onReact={(reactionType) => {
                          setReactionPickerOpen(false);
                          handleCommentReaction(reactionType);
                        }}
                      />
                    )}
                  </span>
                </li>
                {depth === 0 && (
                  <li>
                    <button type="button" className="_comment_reply_btn" onClick={() => setRepliesOpen(true)}>
                      Reply.
                    </button>
                  </li>
                )}
                <li><span>Share</span></li>
                <li><span className="_time_link">.{formatCompactFeedTime(currentComment.createdAt)}</span></li>
              </ul>
            </div>
          </div>

          {depth === 0 && currentComment.replyCount > replies.length && (
            <button type="button" className="_previous_comment_txt _comment_replies_toggle" onClick={() => loadReplies(repliesMeta.next_cursor)} disabled={loadingReplies}>
              {loadingReplies ? 'Loading replies...' : `View ${currentComment.replyCount - replies.length} ${currentComment.replyCount - replies.length === 1 ? 'reply' : 'replies'}`}
            </button>
          )}

          {error && <p className="_bs_comment_error _bs_comment_error_inline">{error}</p>}

          {depth === 0 && repliesOpen && (
            <>
              <CommentBox id={`replyTextarea-${currentComment.id}`} onSubmit={createReply} disabled={replySubmitting} />
              <div className="_comment_replies">
                {replies.map((reply) => (
                  <CommentThreadItem key={reply.id} comment={reply} postId={postId} depth={1} />
                ))}
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
