import { ref } from 'vue';
import { User } from '@/types/models';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { toast } from 'vue-sonner';

export function useUsers() {
  const loading = ref(false);
  const processingId = ref<number | null>(null);
  const selectedUser = ref<User | null>(null);
  const showDeleteConfirm = ref(false);

  const formatDate = (dateString: string) => {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(date);
  };

  const confirmDelete = (user: User) => {
    selectedUser.value = user;
    showDeleteConfirm.value = true;
  };

  const deleteUser = async () => {
    if (!selectedUser.value) return;
    
    processingId.value = selectedUser.value.id;
    
    try {
      await axios.delete(`/admin/users/${selectedUser.value.id}`);
      
      toast.success('Berhasil!', {
        description: `Pengguna ${selectedUser.value.name} berhasil dihapus.`,
      });
      
      showDeleteConfirm.value = false;
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal menghapus pengguna', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat menghapus pengguna.',
      });
    } finally {
      processingId.value = null;
    }
  };

  const createUser = async (data: Partial<User>) => {
    loading.value = true;
    
    try {
      await axios.post('/admin/users', data);
      
      toast.success('Berhasil!', {
        description: 'Pengguna baru berhasil dibuat.',
      });
      
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal membuat pengguna', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat membuat pengguna baru.',
      });
    } finally {
      loading.value = false;
    }
  };

  const updateUser = async (id: number, data: Partial<User>) => {
    processingId.value = id;
    
    try {
      await axios.put(`/admin/users/${id}`, data);
      
      toast.success('Berhasil!', {
        description: 'Pengguna berhasil diperbarui.',
      });
      
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal memperbarui pengguna', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat memperbarui pengguna.',
      });
    } finally {
      processingId.value = null;
    }
  };

  return {
    loading,
    processingId,
    selectedUser,
    showDeleteConfirm,
    formatDate,
    confirmDelete,
    deleteUser,
    createUser,
    updateUser
  };
} 