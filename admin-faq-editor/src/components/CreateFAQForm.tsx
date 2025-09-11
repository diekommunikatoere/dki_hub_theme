import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';
import { faqItemsAPI } from '../services/api';
import WYSIWYGEditor from './WYSIWYGEditor';
import { FAQItem } from '../types';

interface CreateFAQFormProps {
  sectionId: number;
  onFAQCreated: (faqItem: FAQItem) => void;
  onCancel: () => void;
}

const CreateFAQForm: React.FC<CreateFAQFormProps> = ({ 
  sectionId,
  onFAQCreated, 
  onCancel 
}) => {
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!title.trim()) {
      setError(__('FAQ title is required.', 'dki-wiki'));
      return;
    }

    if (!content.trim()) {
      setError(__('FAQ content is required.', 'dki-wiki'));
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const newFAQItem = await faqItemsAPI.create({
        title: title.trim(),
        content: content.trim(),
        sectionId,
      });
      
      onFAQCreated(newFAQItem);
      setTitle('');
      setContent('');
    } catch (error) {
      console.error('Error creating FAQ item:', error);
      setError(__('Failed to create FAQ item. Please try again.', 'dki-wiki'));
    } finally {
      setIsLoading(false);
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Escape') {
      onCancel();
    }
  };

  return (
    <div className="create-faq-form">
      {error && (
        <div className="notice notice-error">
          <p>{error}</p>
        </div>
      )}
      
      <form onSubmit={handleSubmit} onKeyDown={handleKeyDown}>
        <table className="form-table">
          <tbody>
            <tr>
              <th scope="row">
                <label htmlFor="faq-title">
                  {__('Question', 'dki-wiki')} <span className="required">*</span>
                </label>
              </th>
              <td>
                <input
                  type="text"
                  id="faq-title"
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                  className="regular-text"
                  placeholder={__('Enter the FAQ question...', 'dki-wiki')}
                  required
                  disabled={isLoading}
                  autoFocus
                />
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label htmlFor="faq-content">
                  {__('Answer', 'dki-wiki')} <span className="required">*</span>
                </label>
              </th>
              <td>
                <div className="create-faq-form__editor">
                  <WYSIWYGEditor
                    value={content}
                    onChange={setContent}
                    placeholder={__('Enter the FAQ answer...', 'dki-wiki')}
                    disabled={isLoading}
                  />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div className="create-faq-form__actions">
          <button 
            type="submit" 
            className="button button-primary"
            disabled={isLoading || !title.trim() || !content.trim()}
          >
            {isLoading ? __('Creating...', 'dki-wiki') : __('Create FAQ Item', 'dki-wiki')}
          </button>
          <button 
            type="button" 
            className="button"
            onClick={onCancel}
            disabled={isLoading}
          >
            {__('Cancel', 'dki-wiki')}
          </button>
        </div>
      </form>
    </div>
  );
};

export default CreateFAQForm;
