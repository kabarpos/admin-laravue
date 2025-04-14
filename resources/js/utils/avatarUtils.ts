/**
 * Utility functions untuk penanganan avatar user
 */

import type { User } from '@/types';

/**
 * Mengambil URL lengkap dari path foto profil
 * @param path Path foto profil (bisa relatif atau URL lengkap)
 * @returns URL lengkap atau null jika tidak ada path
 */
export const getAvatarUrl = (path: string | null | undefined): string | null => {
  if (!path) return null;
  
  // Jika sudah berupa URL lengkap, gunakan langsung
  if (path.startsWith('http')) return path;
  
  // Jika path relatif, tambahkan URL storage
  return `/storage/${path}`;
};

/**
 * Mendapatkan field avatar yang valid dari data user
 * Memeriksa profile_photo_path terlebih dahulu, kemudian fallback ke avatar
 * @param user Object user yang berisi profile_photo_path dan/atau avatar
 * @returns Path avatar yang valid atau null
 */
export const getUserAvatarPath = (user: User): string | null => {
  if (user?.profile_photo_path) {
    return user.profile_photo_path;
  }
  return user?.avatar || null;
};

/**
 * Validasi file avatar sebelum upload
 * @param file File yang akan di-upload
 * @returns Object berisi status valid dan pesan error jika tidak valid
 */
export const validateAvatarFile = (file: File): { valid: boolean; message?: string } => {
  // Validasi tipe file (hanya gambar)
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  if (!allowedTypes.includes(file.type)) {
    return { 
      valid: false, 
      message: 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.' 
    };
  }
  
  // Validasi ukuran file (maksimal 2MB)
  const maxSize = 2 * 1024 * 1024; // 2MB
  if (file.size > maxSize) {
    return { 
      valid: false, 
      message: 'Ukuran file terlalu besar. Maksimal 2MB.' 
    };
  }
  
  return { valid: true };
};

/**
 * Membuat dan mengelola preview URL untuk avatar
 * Termasuk cleanup untuk mencegah memory leak
 * @param file File gambar
 * @returns URL preview dan fungsi cleanup
 */
export const createAvatarPreview = (file: File): { previewUrl: string; revokePreview: () => void } => {
  const previewUrl = URL.createObjectURL(file);
  
  const revokePreview = () => {
    URL.revokeObjectURL(previewUrl);
  };
  
  return { previewUrl, revokePreview };
}; 