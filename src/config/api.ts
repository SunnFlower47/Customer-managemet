export const API_CONFIG = {
  BASE_URL: process.env.REACT_APP_API_URL || 'https://andrinet.id/api/v1',
  TIMEOUT: 10000,
};

// Debug logging for production
console.log('API Config:', {
  BASE_URL: API_CONFIG.BASE_URL,
  NODE_ENV: process.env.NODE_ENV,
  REACT_APP_API_URL: process.env.REACT_APP_API_URL
});