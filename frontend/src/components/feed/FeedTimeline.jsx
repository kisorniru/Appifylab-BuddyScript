import { useCallback, useEffect, useRef, useState } from 'react';
import { feedApi } from '../../api/feedApi.js';
import { EmptyState, ErrorState, LoadingState } from '../common/ApiState.jsx';
import AssetImage from '../common/AssetImage.jsx';
import { useInfiniteApi } from '../../hooks/useInfiniteApi.js';
import { useIntersectionLoadMore } from '../../hooks/useIntersectionLoadMore.js';
import { postMenuItems } from '../../data/feedStaticData.js';
import { DotsIcon, PostMenuIcon } from './FeedIcons.jsx';
import { CommentBox, CommentThreadItem, ReactionRow, ReactorsModal } from './FeedComments.jsx';
import {
  formatFeedTime,
  getVideoMimeType,
  isVideoMedia,
  normalizeComment,
  normalizeTimelinePost,
} from './feedUtils.js';

export function TimelinePost({ post = null, second = false }) {
  const [postMenuOpen, setPostMenuOpen] = useState(false);
  const [comments, setComments] = useState([]);
  const [commentsMeta, setCommentsMeta] = useState({ has_more: false, next_cursor: null });
  const [commentsLoading, setCommentsLoading] = useState(false);
  const [commentSubmitting, setCommentSubmitting] = useState(false);
  const [reactionSubmitting, setReactionSubmitting] = useState(false);
  const [commentError, setCommentError] = useState('');
  const displayPost = normalizeTimelinePost(post);
  const [postReactionCount, setPostReactionCount] = useState(displayPost.reactionCount);
  const [postReactors, setPostReactors] = useState(displayPost.reactors || []);
  const [postViewerReaction, setPostViewerReaction] = useState(displayPost.viewerReaction);
  const reactionTotal = postReactionCount > 9 ? `${postReactionCount}+` : postReactionCount;
  const [commentTotal, setCommentTotal] = useState(displayPost.commentCount);
  const [reactorsOpen, setReactorsOpen] = useState(false);
  const hasVideoMedia = isVideoMedia(displayPost.image, displayPost.mediaType);
  const commentInputRef = useRef(null);

  useEffect(() => {
    let ignore = false;

    async function loadComments() {
      if (!displayPost.postId) return;

      setCommentsLoading(true);
      setCommentError('');

      try {
        const result = await feedApi.getPostComments({ postId: displayPost.postId, limit: 2 });
        if (!ignore) {
          setComments((result.items || []).map(normalizeComment));
          setCommentsMeta(result.meta || {});
        }
      } catch (error) {
        if (!ignore) setCommentError(error.message || 'Unable to load comments.');
      } finally {
        if (!ignore) setCommentsLoading(false);
      }
    }

    loadComments();

    return () => {
      ignore = true;
    };
  }, [displayPost.postId]);

  async function loadMoreComments() {
    if (!displayPost.postId || commentsLoading || !commentsMeta.next_cursor) return;

    setCommentsLoading(true);
    setCommentError('');

    try {
      const result = await feedApi.getPostComments({
        postId: displayPost.postId,
        cursor: commentsMeta.next_cursor,
        limit: 5,
      });
      setComments((current) => [...current, ...(result.items || []).map(normalizeComment)]);
      setCommentsMeta(result.meta || {});
    } catch (error) {
      setCommentError(error.message || 'Unable to load previous comments.');
    } finally {
      setCommentsLoading(false);
    }
  }

  async function handleCreateComment(payload) {
    if (!displayPost.postId) return;

    setCommentSubmitting(true);
    setCommentError('');

    try {
      const createdComment = await feedApi.createPostComment({
        postId: displayPost.postId,
        body: payload.body,
        media: payload.media,
        mediaType: payload.mediaType,
      });
      setComments((current) => [normalizeComment(createdComment), ...current]);
      setCommentTotal((count) => count + 1);
    } catch (error) {
      setCommentError(error.message || 'Unable to post comment.');
      throw error;
    } finally {
      setCommentSubmitting(false);
    }
  }

  async function handlePostReaction(reactionType) {
    if (!displayPost.postId || reactionSubmitting) return;

    setReactionSubmitting(true);
    setCommentError('');

    try {
      const updatedPost = await feedApi.togglePostReaction({ postId: displayPost.postId, reactionType });
      const normalizedPost = normalizeTimelinePost(updatedPost);
      setPostReactionCount(normalizedPost.reactionCount);
      setPostReactors(normalizedPost.reactors || []);
      setPostViewerReaction(normalizedPost.viewerReaction);
    } catch (error) {
      setCommentError(error.message || 'Unable to update reaction.');
    } finally {
      setReactionSubmitting(false);
    }
  }

  function focusCommentInput() {
    const input = commentInputRef.current;
    if (!input || input.disabled) return;

    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
    window.setTimeout(() => input.focus(), 220);
  }

  function handleCommentCountClick(event) {
    event.preventDefault();
    if (commentsMeta.has_more) {
      loadMoreComments();
    }
    focusCommentInput();
  }

  return (
    <div className="_feed_inner_timeline_post_area _b_radious6 _padd_b24 _padd_t24 _mar_b16">
      <ReactorsModal
        open={reactorsOpen}
        title="Post reactions"
        onClose={() => setReactorsOpen(false)}
        loadItems={({ cursor, limit }) => feedApi.getPostReactionUsers({ postId: displayPost.postId, cursor, limit })}
      />
      <div className="_feed_inner_timeline_content _padd_r24 _padd_l24">
        <div className="_feed_inner_timeline_post_top">
          <div className="_feed_inner_timeline_post_box">
            <div className="_feed_inner_timeline_post_box_image">
              <AssetImage name={displayPost.authorAvatar} alt="" className="_post_img" />
            </div>
            <div className="_feed_inner_timeline_post_box_txt">
              <h4 className="_feed_inner_timeline_post_box_title">{displayPost.authorName}</h4>
              <p className="_feed_inner_timeline_post_box_para">{formatFeedTime(displayPost.createdAt)} . <a href="#0">Public</a></p>
            </div>
          </div>
          <div className="_feed_inner_timeline_post_box_dropdown">
            <div className="_feed_timeline_post_dropdown">
              <button
                type="button"
                className="_feed_timeline_post_dropdown_link"
                aria-label="Post options"
                aria-expanded={postMenuOpen}
                onClick={() => setPostMenuOpen((open) => !open)}
              >
                <DotsIcon />
              </button>
            </div>
            {postMenuOpen && (
              <div className="_feed_timeline_dropdown _timeline_dropdown show">
                <ul className="_feed_timeline_dropdown_list">
                  {postMenuItems.map((item) => (
                    <li className="_feed_timeline_dropdown_item" key={item.label}>
                      <a href="#0" className="_feed_timeline_dropdown_link">
                        <span><PostMenuIcon type={item.icon} /></span>
                        {item.label}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
            )}
          </div>
        </div>
        <h4 className="_feed_inner_timeline_post_title">{displayPost.text}</h4>
        {displayPost.image && (
          <div className="_feed_inner_timeline_image">
            {hasVideoMedia ? (
              <video className="_time_img _time_video" controls preload="metadata">
                <source src={displayPost.image} type={getVideoMimeType(displayPost.image)} />
              </video>
            ) : (
              <AssetImage name={displayPost.image} alt={displayPost.text} className="_time_img" />
            )}
          </div>
        )}
      </div>
      <div className="_feed_inner_timeline_total_reacts _padd_r24 _padd_l24 _mar_b26">
        <button
          type="button"
          className="_feed_inner_timeline_total_reacts_image _reaction_users_trigger"
          disabled={postReactionCount <= 4}
          onClick={() => {
            if (postReactionCount > 4) setReactorsOpen(true);
          }}
          aria-label="View all post reactions"
        >
          {postReactors.slice(0, 4).map((reactor, index) => (
            <AssetImage
              key={`${displayPost.postId}-${reactor.id}`}
              name={reactor.avatar || 'profile.png'}
              alt={reactor.name || ''}
              className={index === 0 ? '_react_img1' : `_react_img${index > 1 ? ' _rect_img_mbl_none' : ''}`}
            />
          ))}
          {postReactionCount > 0 && <span className="_feed_inner_timeline_total_reacts_para">{reactionTotal}</span>}
        </button>
        <div className="_feed_inner_timeline_total_reacts_txt">
          <p className="_feed_inner_timeline_total_reacts_para1"><a href="#0" onClick={handleCommentCountClick}><span>{commentTotal}</span> Comment</a></p>
          <p className="_feed_inner_timeline_total_reacts_para2"><span>{displayPost.shareCount}</span> Share</p>
        </div>
      </div>
      <ReactionRow activeReaction={postViewerReaction} onReact={handlePostReaction} onCommentClick={focusCommentInput} disabled={reactionSubmitting} />
      <div className="_feed_inner_timeline_cooment_area">
        <CommentBox id={second ? 'floatingTextareaSecond' : 'floatingTextareaFirst'} inputRef={commentInputRef} onSubmit={handleCreateComment} disabled={commentSubmitting} />
        {commentError && <p className="_bs_comment_error">{commentError}</p>}
      </div>
      <div className="_timline_comment_main">
        {commentsMeta.has_more && (
          <div className="_previous_comment">
            <button type="button" className="_previous_comment_txt" onClick={loadMoreComments} disabled={commentsLoading}>
              {commentsLoading ? 'Loading previous comments...' : 'View previous comments'}
            </button>
          </div>
        )}
        {commentsLoading && !comments.length && (
          <div className="_previous_comment">
            <span className="_previous_comment_txt">Loading comments...</span>
          </div>
        )}
        {comments.map((comment) => (
          <CommentThreadItem
            key={comment.id}
            comment={comment}
            postId={displayPost.postId}
            onReplyCreated={() => setCommentTotal((count) => count + 1)}
          />
        ))}
      </div>
    </div>
  );
}

export function FeedTimeline() {
  const loadFeed = useCallback((params) => feedApi.getUserFeed(params), []);
  const infiniteState = useInfiniteApi(loadFeed, { limit: 10 });
  const { items, initialLoading, loading, error, hasMore, loadMore } = infiniteState;
  const loadMoreRef = useIntersectionLoadMore({
    enabled: hasMore && !loading && !initialLoading,
    onLoadMore: loadMore,
  });

  if (initialLoading) {
    return <LoadingState label="Loading first feed page from API..." />;
  }

  if (error && !items.length) {
    return <ErrorState error={error} />;
  }

  if (!items.length) {
    return <EmptyState title="No feed posts found" subtitle="Your timeline will appear here when feed data is available." />;
  }

  return (
    <>
      {items.map((post, index) => (
        <TimelinePost key={post.id} post={post} second={index % 2 === 1} />
      ))}

      {error && <ErrorState error={error} />}
      <div ref={loadMoreRef} className="feed-load-more-sentinel" aria-hidden="true" />
      {loading && <LoadingState label="Loading more posts..." />}
      {!hasMore && <div className="api-state text-center"><strong>You are all caught up</strong><span>No more posts right now.</span></div>}
    </>
  );
}

