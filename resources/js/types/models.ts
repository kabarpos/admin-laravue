export interface PaginatedData<T> {
  data: T[];
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
  created_at: string;
  updated_at: string;
  roles?: Role[];
  permissions?: Permission[];
}

export interface Role {
  id: number;
  name: string;
  guard_name: string;
  created_at: string;
  updated_at: string;
  permissions?: Permission[];
  users_count?: number;
}

export interface Permission {
  id: number;
  name: string;
  guard_name: string;
  created_at: string;
  updated_at: string;
}

export interface EmailSetting {
  id: number;
  mail_mailer: string;
  mail_host: string;
  mail_port: number;
  mail_username: string;
  mail_password: string;
  mail_encryption: string;
  mail_from_address: string;
  mail_from_name: string;
  verification_enabled: boolean;
  verification_template?: string;
  created_at: string;
  updated_at: string;
} 