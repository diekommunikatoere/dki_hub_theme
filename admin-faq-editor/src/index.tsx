import React from 'react';
import { createRoot } from 'react-dom/client';
import FAQEditor from './components/FAQEditor';
import { FAQProvider } from './context/FAQContext';
import { setupAPI } from './services/api';
import { WordPressConfig } from './types';

// Import styles
import './styles/index.scss';

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('dki-faq-editor-root');
  
  if (!container) {
    console.error('FAQ Editor container not found');
    return;
  }

  // Get WordPress configuration
  const wpConfig = (window as any).dkiFAQEditor as WordPressConfig;
  
  if (!wpConfig) {
    console.error('WordPress configuration not found');
    container.innerHTML = `
      <div class="notice notice-error">
        <p><strong>Error:</strong> WordPress configuration not loaded. Please refresh the page.</p>
      </div>
    `;
    return;
  }

  // Setup API with WordPress configuration
  setupAPI({
    apiUrl: wpConfig.apiUrl,
    nonce: wpConfig.nonce,
  });

  // Create React root and render app
  const root = createRoot(container);
  
  root.render(
    <React.StrictMode>
      <FAQProvider config={wpConfig}>
        <FAQEditor />
      </FAQProvider>
    </React.StrictMode>
  );
});
