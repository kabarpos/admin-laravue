/**
 * Type definitions for database models
 */

export interface BaseModel {
  id: number;
  created_at: string;
  updated_at: string;
}

export interface User extends BaseModel {
  name: string;
  email: string;
  whatsapp: string | null;
  email_verified_at: string | null;
  status: 'active' | 'inactive' | 'blocked' | 'rejected';
  roles?: Role[];
}

export interface Role extends BaseModel {
  name: string;
  guard_name: string;
  permissions?: Permission[];
  users_count?: number;
}

export interface Permission extends BaseModel {
  name: string;
  guard_name: string;
  roles_count?: number;
}

export interface EmailSetting extends BaseModel {
  mail_driver: string;
  mail_host: string | null;
  mail_port: string | null;
  mail_username: string | null;
  mail_password: string | null;
  mail_encryption: string | null;
  mail_from_address: string | null;
  mail_from_name: string | null;
  enable_verification: boolean;
  verification_template: string | null;
}

/**
 * Type definitions for pagination
 */

export interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  links: PaginationLink[];
  path: string;
  per_page: number;
  to: number | null;
  total: number;
}

export interface PaginatedData<T> {
  data: T[];
  links: PaginationLink[];
  meta: PaginationMeta;
} 