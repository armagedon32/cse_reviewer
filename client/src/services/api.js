const API_BASE = import.meta.env.VITE_API_URL || 
  (import.meta.env.PROD ? window.location.origin : '/api/quizzes');

const baseURL = API_BASE === '/api/quizzes' ? '' : API_BASE;

export const api = {
  async getQuizzes() {
    const res = await fetch(`${baseURL}/api/quizzes`);
    if (!res.ok) throw new Error('Failed to fetch quizzes');
    return res.json();
  },

  async getQuiz(id) {
    const res = await fetch(`${baseURL}/api/quizzes/${id}`);
    if (!res.ok) throw new Error('Failed to fetch quiz');
    return res.json();
  },

  async createQuiz(quiz) {
    const res = await fetch(`${baseURL}/api/quizzes`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(quiz)
    });
    if (!res.ok) throw new Error('Failed to create quiz');
    return res.json();
  },

  async updateQuiz(id, quiz) {
    const res = await fetch(`${baseURL}/api/quizzes/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(quiz)
    });
    if (!res.ok) throw new Error('Failed to update quiz');
    return res.json();
  },

  async deleteQuiz(id) {
    const res = await fetch(`${baseURL}/api/quizzes/${id}`, {
      method: 'DELETE'
    });
    if (!res.ok) throw new Error('Failed to delete quiz');
    return res.json();
  },

  async getCategories() {
    const res = await fetch(`${baseURL}/api/categories`);
    if (!res.ok) throw new Error('Failed to fetch categories');
    return res.json();
  }
};