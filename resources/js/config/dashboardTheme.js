/**
 * Dashboard Theme Configuration
 * Color scheme: Yellow (#fbbf24) and Blue (#3b82f6)
 */

export const colors = {
    primary: {
        yellow: '#fbbf24',
        blue: '#3b82f6',
        darkBlue: '#1e40af',
        accentYellow: '#f59e0b',
    },
    text: {
        dark: '#111827',
        light: '#6b7280',
        muted: '#9ca3af',
    },
    background: {
        light: '#f9fafb',
        lighter: '#f3f4f6',
        white: '#ffffff',
    },
    status: {
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6',
    },
};

export const gradients = {
    primary: 'linear-gradient(135deg, #fbbf24 0%, #3b82f6 100%)',
    primaryReverse: 'linear-gradient(135deg, #3b82f6 0%, #fbbf24 100%)',
    header: 'linear-gradient(135deg, #3b82f6 0%, #1e40af 100%)',
    yellowBlue: 'linear-gradient(90deg, #fbbf24 0%, #3b82f6 100%)',
    success: 'linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%)',
    error: 'linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%)',
    neutral: 'linear-gradient(135deg, rgba(107, 114, 128, 0.1) 0%, rgba(107, 114, 128, 0.05) 100%)',
};

export const shadows = {
    sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
    md: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
    lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
    xl: '0 20px 25px -5px rgba(0, 0, 0, 0.1)',
    glow: '0 0 20px rgba(251, 191, 36, 0.4)',
};

export const animations = {
    durations: {
        fast: 0.2,
        normal: 0.3,
        slow: 0.5,
        slowest: 0.8,
    },
    timing: {
        linear: 'linear',
        ease: 'ease',
        easeIn: 'ease-in',
        easeOut: 'ease-out',
        easeInOut: 'ease-in-out',
        cubic: [0.4, 0, 0.2, 1],
        elasticOut: [0.34, 1.56, 0.64, 1],
    },
};

export const breakpoints = {
    mobile: '480px',
    tablet: '768px',
    desktop: '1024px',
    wide: '1280px',
};

/**
 * CSS Variable Declaration
 * To use in CSS, import this and declare variables
 */
export const getCSSVariables = () => {
    return `
        :root {
            --primary-yellow: ${colors.primary.yellow};
            --primary-blue: ${colors.primary.blue};
            --dark-blue: ${colors.primary.darkBlue};
            --accent-yellow: ${colors.primary.accentYellow};

            --text-dark: ${colors.text.dark};
            --text-light: ${colors.text.light};
            --text-muted: ${colors.text.muted};

            --bg-light: ${colors.background.light};
            --bg-lighter: ${colors.background.lighter};
            --bg-white: ${colors.background.white};

            --status-success: ${colors.status.success};
            --status-error: ${colors.status.error};
            --status-warning: ${colors.status.warning};
            --status-info: ${colors.status.info};

            --shadow-sm: ${shadows.sm};
            --shadow-md: ${shadows.md};
            --shadow-lg: ${shadows.lg};
            --shadow-xl: ${shadows.xl};
            --shadow-glow: ${shadows.glow};
        }
    `;
};

export const themeConfig = {
    colors,
    gradients,
    shadows,
    animations,
    breakpoints,
};

export default themeConfig;
