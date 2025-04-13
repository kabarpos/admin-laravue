/**
 * Fungsi untuk mendapatkan inisial dari nama lengkap
 * @param fullName Nama lengkap
 * @returns Inisial nama (1-2 karakter)
 */
export function getInitials(fullName?: string): string {
    if (!fullName) return '';

    const names = fullName.trim().split(' ');

    if (names.length === 0) return '';
    if (names.length === 1) return names[0].charAt(0).toUpperCase();

    return `${names[0].charAt(0)}${names[names.length - 1].charAt(0)}`.toUpperCase();
}

/**
 * Composable untuk mendapatkan inisial dari nama
 * @returns Object dengan fungsi getInitials
 */
export function useInitials() {
    return { getInitials };
}
