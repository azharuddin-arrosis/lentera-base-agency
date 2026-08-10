/**
 * Utilitas format angka finansial untuk tampilan PnL.
 */

export function formatUsd(value: number | string | null | undefined, withSign = false): string {
    const n = Number(value ?? 0);
    const abs = Math.abs(n);
    const formatted = abs.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
    const sign = n < 0 ? '-' : withSign ? '+' : '';
    return `${sign}$${formatted}`;
}

export function formatNumber(value: number | string | null | undefined, digits = 0): string {
    return Number(value ?? 0).toLocaleString('en-US', {
        minimumFractionDigits: digits,
        maximumFractionDigits: digits,
    });
}

export function formatPercent(value: number | string | null | undefined, withSign = false): string {
    const n = Number(value ?? 0);
    const sign = n > 0 && withSign ? '+' : '';
    return `${sign}${n.toFixed(2)}%`;
}

/** Kelas warna Tailwind untuk nilai profit (hijau) / loss (merah) / netral. */
export function pnlColor(value: number | string | null | undefined): string {
    const n = Number(value ?? 0);
    if (n > 0) return 'text-emerald-600 dark:text-emerald-400';
    if (n < 0) return 'text-rose-600 dark:text-rose-400';
    return 'text-muted-foreground';
}

/** Format Rupiah, mis. 500000 -> "Rp500.000". */
export function formatIdr(value: number | string | null | undefined): string {
    const n = Number(value ?? 0);
    return 'Rp' + n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
}

/** Format harga instrumen dengan presisi adaptif. */
export function formatPrice(value: number | string | null | undefined): string {
    const n = Number(value ?? 0);
    if (n === 0) return '-';
    const digits = n >= 100 ? 2 : n >= 1 ? 3 : 5;
    return n.toLocaleString('en-US', { minimumFractionDigits: digits, maximumFractionDigits: digits });
}
