import { ref } from 'vue';
import { toast } from 'vue-sonner';

export function useToast() {
  const showToast = ({ 
    title, 
    description, 
    variant = 'default', 
    duration = 5000 
  }: {
    title?: string;
    description?: string;
    variant?: 'default' | 'destructive' | 'success';
    duration?: number;
  }) => {
    if (variant === 'success') {
      toast.success(title || 'Berhasil', {
        description,
        duration,
      });
    } else if (variant === 'destructive') {
      toast.error(title || 'Gagal', {
        description,
        duration,
      });
    } else {
      toast(title || '', {
        description,
        duration,
      });
    }
  };

  return {
    toast: showToast
  };
} 