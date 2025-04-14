import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { getAvatarUrl, getUserAvatarPath, validateAvatarFile, createAvatarPreview } from '@/utils/avatarUtils';
import type { User } from '@/types';

describe('avatarUtils', () => {
  describe('getAvatarUrl', () => {
    it('returns null for null input', () => {
      expect(getAvatarUrl(null)).toBeNull();
    });

    it('returns null for undefined input', () => {
      expect(getAvatarUrl(undefined)).toBeNull();
    });

    it('returns the original URL for http URLs', () => {
      const url = 'http://example.com/avatar.jpg';
      expect(getAvatarUrl(url)).toBe(url);
    });

    it('returns the original URL for https URLs', () => {
      const url = 'https://example.com/avatar.jpg';
      expect(getAvatarUrl(url)).toBe(url);
    });

    it('returns a path with /storage/ prefix for relative paths', () => {
      const path = 'avatars/user1.jpg';
      expect(getAvatarUrl(path)).toBe('/storage/avatars/user1.jpg');
    });
  });

  describe('getUserAvatarPath', () => {
    it('returns profile_photo_path if present', () => {
      const user = {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        profile_photo_path: 'avatars/john.jpg',
        avatar: 'old-avatars/john.jpg',
        email_verified_at: null
      } as User;

      expect(getUserAvatarPath(user)).toBe('avatars/john.jpg');
    });

    it('returns avatar if profile_photo_path is not present', () => {
      const user = {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        profile_photo_path: null,
        avatar: 'old-avatars/john.jpg',
        email_verified_at: null
      } as User;

      expect(getUserAvatarPath(user)).toBe('old-avatars/john.jpg');
    });

    it('returns null if neither profile_photo_path nor avatar is present', () => {
      const user = {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        profile_photo_path: null,
        email_verified_at: null
      } as User;

      expect(getUserAvatarPath(user)).toBeNull();
    });
  });

  describe('validateAvatarFile', () => {
    it('returns valid for jpeg files under size limit', () => {
      const file = new File(['test'], 'avatar.jpg', { type: 'image/jpeg' });
      Object.defineProperty(file, 'size', { value: 1024 * 1024 }); // 1MB

      expect(validateAvatarFile(file)).toEqual({ valid: true });
    });

    it('returns valid for png files under size limit', () => {
      const file = new File(['test'], 'avatar.png', { type: 'image/png' });
      Object.defineProperty(file, 'size', { value: 1024 * 1024 }); // 1MB

      expect(validateAvatarFile(file)).toEqual({ valid: true });
    });

    it('returns invalid for non-image files', () => {
      const file = new File(['test'], 'document.pdf', { type: 'application/pdf' });
      
      const result = validateAvatarFile(file);
      expect(result.valid).toBe(false);
      expect(result.message).toContain('Format file tidak didukung');
    });

    it('returns invalid for files over size limit', () => {
      const file = new File(['test'], 'avatar.jpg', { type: 'image/jpeg' });
      Object.defineProperty(file, 'size', { value: 3 * 1024 * 1024 }); // 3MB
      
      const result = validateAvatarFile(file);
      expect(result.valid).toBe(false);
      expect(result.message).toContain('Ukuran file terlalu besar');
    });
  });

  describe('createAvatarPreview', () => {
    const originalCreateObjectURL = URL.createObjectURL;
    const originalRevokeObjectURL = URL.revokeObjectURL;

    beforeEach(() => {
      URL.createObjectURL = vi.fn().mockReturnValue('blob:test-url');
      URL.revokeObjectURL = vi.fn();
    });

    afterEach(() => {
      URL.createObjectURL = originalCreateObjectURL;
      URL.revokeObjectURL = originalRevokeObjectURL;
    });

    it('creates preview URL and revoke function', () => {
      const file = new File(['test'], 'avatar.jpg', { type: 'image/jpeg' });
      
      const { previewUrl, revokePreview } = createAvatarPreview(file);
      
      expect(previewUrl).toBe('blob:test-url');
      expect(URL.createObjectURL).toHaveBeenCalledWith(file);
      
      // Test the revoke function
      revokePreview();
      expect(URL.revokeObjectURL).toHaveBeenCalledWith('blob:test-url');
    });
  });
}); 