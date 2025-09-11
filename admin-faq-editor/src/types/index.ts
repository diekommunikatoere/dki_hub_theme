export interface FAQSection {
  id: number;
  name: string;
  slug: string;
  description?: string;
  order: number;
  count: number;
}

export interface FAQItem {
  id: number;
  title: string;
  content: string;
  sectionId: number;
  order: number;
  status: 'publish' | 'draft' | 'private';
  dateCreated: string;
  dateModified: string;
}

export interface CreateSectionData {
  name: string;
  description?: string;
}

export interface CreateFAQData {
  title: string;
  content: string;
  sectionId: number;
}

export interface UpdateSectionData extends Partial<CreateSectionData> {
  id: number;
}

export interface UpdateFAQData extends Partial<CreateFAQData> {
  id: number;
}

export interface ReorderData {
  sectionsOrder?: number[];
  faqsOrder?: { [sectionId: number]: number[] };
}

export interface WordPressConfig {
  apiUrl: string;
  nonce: string;
  currentUser: {
    id: number;
    name: string;
    capabilities: string[];
  };
  labels: {
    [key: string]: string;
  };
}

export interface APIResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
}
