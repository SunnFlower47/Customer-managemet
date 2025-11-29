/**
 * Simple in-memory cache service for API responses
 * Provides TTL (Time To Live) and automatic cache invalidation
 */

interface CacheEntry<T> {
  data: T;
  timestamp: number;
  ttl: number; // Time to live in milliseconds
}

class CacheService {
  private cache: Map<string, CacheEntry<any>> = new Map();

  /**
   * Get cached data if available and not expired
   */
  get<T>(key: string): T | null {
    const entry = this.cache.get(key);
    
    if (!entry) {
      return null;
    }

    const now = Date.now();
    const isExpired = now - entry.timestamp > entry.ttl;

    if (isExpired) {
      this.cache.delete(key);
      return null;
    }

    return entry.data as T;
  }

  /**
   * Set cache with TTL
   * @param key Cache key
   * @param data Data to cache
   * @param ttl Time to live in milliseconds (default: 5 minutes)
   */
  set<T>(key: string, data: T, ttl: number = 5 * 60 * 1000): void {
    this.cache.set(key, {
      data,
      timestamp: Date.now(),
      ttl,
    });
  }

  /**
   * Remove specific cache entry
   */
  remove(key: string): void {
    this.cache.delete(key);
  }

  /**
   * Clear all cache
   */
  clear(): void {
    this.cache.clear();
  }

  /**
   * Clear cache by pattern (e.g., 'payment:*')
   */
  clearByPattern(pattern: string): void {
    const regex = new RegExp(pattern.replace('*', '.*'));
    const keysToDelete: string[] = [];

    this.cache.forEach((_, key) => {
      if (regex.test(key)) {
        keysToDelete.push(key);
      }
    });

    keysToDelete.forEach(key => this.cache.delete(key));
  }

  /**
   * Check if cache exists and is valid
   */
  has(key: string): boolean {
    const entry = this.cache.get(key);
    if (!entry) {
      return false;
    }

    const now = Date.now();
    const isExpired = now - entry.timestamp > entry.ttl;

    if (isExpired) {
      this.cache.delete(key);
      return false;
    }

    return true;
  }

  /**
   * Get cache age in milliseconds
   */
  getAge(key: string): number | null {
    const entry = this.cache.get(key);
    if (!entry) {
      return null;
    }

    return Date.now() - entry.timestamp;
  }
}

// Singleton instance
export const cacheService = new CacheService();

// Cache keys constants
export const CACHE_KEYS = {
  PAYMENT_METHODS: 'payment:methods',
  STATISTICS: 'profile:statistics',
  UNPAID_BILLS: 'payment:unpaid-bills',
  PAYMENT_HISTORY: (page: number) => `payment:history:${page}`,
  PROFILE: 'profile:data',
  TICKETS: (status: string, page: number) => `tickets:${status}:${page}`,
} as const;

