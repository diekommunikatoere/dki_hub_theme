import React, { createContext, useContext, useReducer, useCallback, ReactNode } from 'react';
import { FAQSection, FAQItem, WordPressConfig } from '../types';

interface FAQState {
  sections: FAQSection[];
  faqItems: { [sectionId: number]: FAQItem[] };
  loading: boolean;
  error: string | null;
  config: WordPressConfig | null;
}

type FAQAction = 
  | { type: 'SET_LOADING'; payload: boolean }
  | { type: 'SET_ERROR'; payload: string | null }
  | { type: 'SET_CONFIG'; payload: WordPressConfig }
  | { type: 'SET_SECTIONS'; payload: FAQSection[] }
  | { type: 'ADD_SECTION'; payload: FAQSection }
  | { type: 'UPDATE_SECTION'; payload: FAQSection }
  | { type: 'DELETE_SECTION'; payload: number }
  | { type: 'REORDER_SECTIONS'; payload: FAQSection[] }
  | { type: 'SET_FAQ_ITEMS'; payload: { sectionId: number; items: FAQItem[] } }
  | { type: 'ADD_FAQ_ITEM'; payload: FAQItem }
  | { type: 'UPDATE_FAQ_ITEM'; payload: FAQItem }
  | { type: 'DELETE_FAQ_ITEM'; payload: { itemId: number; sectionId: number } }
  | { type: 'REORDER_FAQ_ITEMS'; payload: { sectionId: number; items: FAQItem[] } };

const initialState: FAQState = {
  sections: [],
  faqItems: {},
  loading: false,
  error: null,
  config: null,
};

const faqReducer = (state: FAQState, action: FAQAction): FAQState => {
  switch (action.type) {
    case 'SET_LOADING':
      return { ...state, loading: action.payload };
    
    case 'SET_ERROR':
      return { ...state, error: action.payload };
    
    case 'SET_CONFIG':
      return { ...state, config: action.payload };
    
    case 'SET_SECTIONS':
      return { ...state, sections: action.payload };
    
    case 'ADD_SECTION':
      return { 
        ...state, 
        sections: [...state.sections, action.payload].sort((a, b) => a.order - b.order) 
      };
    
    case 'UPDATE_SECTION':
      return {
        ...state,
        sections: state.sections.map(section => 
          section.id === action.payload.id ? action.payload : section
        ),
      };
    
    case 'DELETE_SECTION':
      const { [action.payload]: deletedSection, ...remainingFaqItems } = state.faqItems;
      return {
        ...state,
        sections: state.sections.filter(section => section.id !== action.payload),
        faqItems: remainingFaqItems,
      };
    
    case 'REORDER_SECTIONS':
      return { ...state, sections: action.payload };
    
    case 'SET_FAQ_ITEMS':
      return {
        ...state,
        faqItems: {
          ...state.faqItems,
          [action.payload.sectionId]: action.payload.items,
        },
      };
    
    case 'ADD_FAQ_ITEM':
      const sectionItems = state.faqItems[action.payload.sectionId] || [];
      return {
        ...state,
        faqItems: {
          ...state.faqItems,
          [action.payload.sectionId]: [...sectionItems, action.payload].sort((a, b) => a.order - b.order),
        },
      };
    
    case 'UPDATE_FAQ_ITEM':
      return {
        ...state,
        faqItems: {
          ...state.faqItems,
          [action.payload.sectionId]: (state.faqItems[action.payload.sectionId] || []).map(item => 
            item.id === action.payload.id ? action.payload : item
          ),
        },
      };
    
    case 'DELETE_FAQ_ITEM':
      return {
        ...state,
        faqItems: {
          ...state.faqItems,
          [action.payload.sectionId]: (state.faqItems[action.payload.sectionId] || []).filter(
            item => item.id !== action.payload.itemId
          ),
        },
      };
    
    case 'REORDER_FAQ_ITEMS':
      return {
        ...state,
        faqItems: {
          ...state.faqItems,
          [action.payload.sectionId]: action.payload.items,
        },
      };
    
    default:
      return state;
  }
};

interface FAQContextType {
  state: FAQState;
  dispatch: React.Dispatch<FAQAction>;
  // Helper methods
  setLoading: (loading: boolean) => void;
  setError: (error: string | null) => void;
  setSections: (sections: FAQSection[]) => void;
  setFAQItems: (sectionId: number, items: FAQItem[]) => void;
}

const FAQContext = createContext<FAQContextType | null>(null);

export const FAQProvider: React.FC<{ children: ReactNode; config: WordPressConfig }> = ({ 
  children, 
  config 
}) => {
  const [state, dispatch] = useReducer(faqReducer, {
    ...initialState,
    config,
  });

  // Helper methods
  const setLoading = useCallback((loading: boolean) => {
    dispatch({ type: 'SET_LOADING', payload: loading });
  }, []);

  const setError = useCallback((error: string | null) => {
    dispatch({ type: 'SET_ERROR', payload: error });
  }, []);

  const setSections = useCallback((sections: FAQSection[]) => {
    dispatch({ type: 'SET_SECTIONS', payload: sections });
  }, []);

  const setFAQItems = useCallback((sectionId: number, items: FAQItem[]) => {
    dispatch({ type: 'SET_FAQ_ITEMS', payload: { sectionId, items } });
  }, []);

  const value: FAQContextType = {
    state,
    dispatch,
    setLoading,
    setError,
    setSections,
    setFAQItems,
  };

  return (
    <FAQContext.Provider value={value}>
      {children}
    </FAQContext.Provider>
  );
};

export const useFAQ = (): FAQContextType => {
  const context = useContext(FAQContext);
  if (!context) {
    throw new Error('useFAQ must be used within a FAQProvider');
  }
  return context;
};
