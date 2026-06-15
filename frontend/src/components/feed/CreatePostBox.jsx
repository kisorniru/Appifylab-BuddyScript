import { useState } from 'react';
import { feedApi } from '../../api/feedApi';
import AssetImage from '../common/AssetImage.jsx';

export default function CreatePostBox({ onCreated }) {
  const [body, setBody] = useState('');
  const [status, setStatus] = useState('idle');

  async function handleSubmit(event) {
    event.preventDefault();
    if (!body.trim()) return;
    setStatus('submitting');
    try {
      await feedApi.createPost({ body });
      setBody('');
      setStatus('success');
      onCreated?.();
    } catch {
      setStatus('error');
    }
  }

  return (
    <form className="smart-create-post" onSubmit={handleSubmit}>
      <div className="smart-create-row">
        <AssetImage name="profile.png" />
        <textarea value={body} onChange={(event) => setBody(event.target.value)} placeholder="What is happening?" rows="3" />
      </div>
      <div className="smart-create-actions">
        <span>{status === 'error' ? 'Post API is not connected yet.' : 'Posts will be submitted to POST /posts.'}</span>
        <button type="submit" className="btn btn-primary" disabled={status === 'submitting'}>{status === 'submitting' ? 'Posting...' : 'Post'}</button>
      </div>
    </form>
  );
}
