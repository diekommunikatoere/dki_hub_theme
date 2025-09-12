import apiFetch from "@wordpress/api-fetch";
import { FAQSection, FAQItem, CreateSectionData, CreateFAQData, UpdateSectionData, UpdateFAQData, ReorderData, APIResponse } from "../types";

// Set up API configuration
export const setupAPI = (config: { apiUrl: string; nonce: string }) => {
	apiFetch.use(apiFetch.createNonceMiddleware(config.nonce));
	apiFetch.use(apiFetch.createRootURLMiddleware(config.apiUrl));
};

// Section API methods
export const sectionsAPI = {
	async getAll(): Promise<FAQSection[]> {
		return apiFetch({
			path: "/dki/v1/faq-sections",
		});
	},

	async create(data: CreateSectionData): Promise<FAQSection> {
		return apiFetch({
			path: "/dki/v1/faq-sections",
			method: "POST",
			data,
		});
	},

	async update(data: UpdateSectionData): Promise<FAQSection> {
		return apiFetch({
			path: `/dki/v1/faq-sections/${data.id}`,
			method: "PUT",
			data,
		});
	},

	async delete(id: number): Promise<APIResponse> {
		return apiFetch({
			path: `/dki/v1/faq-sections/${id}`,
			method: "DELETE",
		});
	},

	async reorder(sectionsOrder: number[]): Promise<APIResponse> {
		return apiFetch({
			path: "/dki/v1/faq-sections/reorder",
			method: "POST",
			data: { sectionsOrder },
		});
	},
};

// FAQ Items API methods
export const faqItemsAPI = {
	async getBySection(sectionId: number): Promise<FAQItem[]> {
		return apiFetch({
			path: `/dki/v1/faq-items?section=${sectionId}`,
		});
	},

	async getAll(): Promise<FAQItem[]> {
		return apiFetch({
			path: "/dki/v1/faq-items",
		});
	},

	async create(data: CreateFAQData): Promise<FAQItem> {
		return apiFetch({
			path: "/dki/v1/faq-items",
			method: "POST",
			data,
		});
	},

	async update(data: UpdateFAQData): Promise<FAQItem> {
		return apiFetch({
			path: `/dki/v1/faq-items/${data.id}`,
			method: "PUT",
			data,
		});
	},

	async delete(id: number): Promise<APIResponse> {
		return apiFetch({
			path: `/dki/v1/faq-items/${id}`,
			method: "DELETE",
		});
	},

	async reorder(sectionId: number, faqsOrder: number[]): Promise<APIResponse> {
		return apiFetch({
			path: "/dki/v1/faq-items/reorder",
			method: "POST",
			data: { sectionId, faqsOrder },
		});
	},
};

// Bulk operations
export const bulkAPI = {
	async reorderAll(data: ReorderData): Promise<APIResponse> {
		return apiFetch({
			path: "/dki/v1/faq-bulk/reorder",
			method: "POST",
			data,
		});
	},
};
