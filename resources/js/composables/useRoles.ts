import { ref } from 'vue';
import { Role } from '@/types/models';
import axios from 'axios';
import { toast } from 'vue-sonner';

export function useRoles() {
  const loading = ref(false);
  const processingId = ref<number | null>(null);
  const selectedRole = ref<Role | null>(null);
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

  const confirmDelete = (role: Role) => {
    selectedRole.value = role;
    showDeleteConfirm.value = true;
  };

  const deleteRole = async () => {
    if (!selectedRole.value) return;
    
    processingId.value = selectedRole.value.id;
    
    try {
      await axios.delete(`/admin/roles/${selectedRole.value.id}`);
      
      toast.success('Berhasil!', {
        description: `Peran ${selectedRole.value.name} berhasil dihapus.`
      });
      
      showDeleteConfirm.value = false;
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal menghapus peran', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat menghapus peran.'
      });
    } finally {
      processingId.value = null;
    }
  };

  const createRole = async (data: Partial<Role>) => {
    loading.value = true;
    
    try {
      await axios.post('/admin/roles', data);
      
      toast.success('Berhasil!', {
        description: 'Peran baru berhasil dibuat.'
      });
      
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal membuat peran', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat membuat peran baru.'
      });
    } finally {
      loading.value = false;
    }
  };

  const updateRole = async (id: number, data: Partial<Role>) => {
    processingId.value = id;
    
    try {
      await axios.put(`/admin/roles/${id}`, data);
      
      toast.success('Berhasil!', {
        description: 'Peran berhasil diperbarui.'
      });
      
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal memperbarui peran', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat memperbarui peran.'
      });
    } finally {
      processingId.value = null;
    }
  };

  const syncPermissions = async (roleId: number, permissionIds: number[]) => {
    processingId.value = roleId;
    
    try {
      await axios.post(`/admin/roles/${roleId}/permissions`, { 
        permissions: permissionIds 
      });
      
      toast.success('Berhasil!', {
        description: 'Perizinan peran berhasil diperbarui.'
      });
      
      window.location.reload();
    } catch (error: any) {
      toast.error('Gagal memperbarui perizinan peran', {
        description: error.response?.data?.message || 'Terjadi kesalahan saat memperbarui perizinan peran.'
      });
    } finally {
      processingId.value = null;
    }
  };

  return {
    loading,
    processingId,
    selectedRole,
    showDeleteConfirm,
    formatDate,
    confirmDelete,
    deleteRole,
    createRole,
    updateRole,
    syncPermissions
  };
} 