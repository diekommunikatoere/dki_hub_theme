import React from 'react';

interface ErrorMessageProps {
  message: string;
  onDismiss?: () => void;
  type?: 'error' | 'warning' | 'info';
}

const ErrorMessage: React.FC<ErrorMessageProps> = ({ 
  message, 
  onDismiss, 
  type = 'error' 
}) => {
  return (
    <div className={`notice notice-${type} ${onDismiss ? 'is-dismissible' : ''}`}>
      <p>{message}</p>
      {onDismiss && (
        <button 
          type="button" 
          className="notice-dismiss"
          onClick={onDismiss}
        >
          <span className="screen-reader-text">Dismiss this notice.</span>
        </button>
      )}
    </div>
  );
};

export default ErrorMessage;
