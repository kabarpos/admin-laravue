import { ref } from 'vue';
import { Permission } from '@/types/models';
import axios from 'axios';
import { toast } from 'vue-sonner';

export function usePermissions() {
  const loading = ref(false);
  const processingId = ref<number | null>(null);
  const selectedPermission = ref<Permission | null>(null);
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

  const confirmDelete = (permission: Permission) => {
    selectedPermission.value = permission;
    showDeleteConfirm.value = true;
  };

  const deletePermission = async () => {
    if (!selectedPermission.value) return;
    
    processingId.value = selectedPermission.value.id;
    
    try {
      await axios.delete(`/admin/permissions/${selectedPermission.value.id}`);
      
      toast.success('Berhasil!', {
        description: `Perizinan ${selectedPermission.value.name} berhasil dihapus.`,
      });
      
      showDeleteConfirm.value = false;
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal menghapus perizinan', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat menghapus perizinan.',
      });
    } finally {
      processingId.value = null;
    }
  };

  const createPermission = async (data: Partial<Permission>) => {
    loading.value = true;
    
    try {
      await axios.post('/admin/permissions', data);
      
      toast.success('Berhasil!', {
        description: 'Perizinan baru berhasil dibuat.',
      });
      
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal membuat perizinan', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat membuat perizinan baru.',
      });
    } finally {
      loading.value = false;
    }
  };

  const updatePermission = async (id: number, data: Partial<Permission>) => {
    processingId.value = id;
    
    try {
      await axios.put(`/admin/permissions/${id}`, data);
      
      toast.success('Berhasil!', {
        description: 'Perizinan berhasil diperbarui.',
      });
      
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal memperbarui perizinan', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat memperbarui perizinan.',
      });
    } finally {
      processingId.value = null;
    }
  };

  return {
    loading,
    processingId,
    selectedPermission,
    showDeleteConfirm,
    formatDate,
    confirmDelete,
    deletePermission,
    createPermission,
    updatePermission
  };
} 