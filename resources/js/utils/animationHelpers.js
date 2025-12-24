/**
 * Animation Helper Functions
 * Utility functions for working with Framer Motion animations
 */

/**
 * Create a staggered delay for items in a list
 * @param {number} index - Index of the item
 * @param {number} baseDelay - Base delay in seconds
 * @param {number} itemDelay - Delay between items in seconds
 * @returns {number} Calculated delay
 */
export const createStaggerDelay = (index, baseDelay = 0, itemDelay = 0.1) => {
    return baseDelay + index * itemDelay;
};

/**
 * Generate random delay for varied animation timing
 * @param {number} min - Minimum delay
 * @param {number} max - Maximum delay
 * @returns {number} Random delay
 */
export const randomDelay = (min = 0, max = 0.3) => {
    return Math.random() * (max - min) + min;
};

/**
 * Create a bounce easing curve
 * @param {number} strength - Bounce strength (0-1)
 * @returns {array} Cubic bezier easing values
 */
export const createBounceEasing = (strength = 0.5) => {
    const elastic = 1.56 * strength;
    return [0.34, elastic, 0.64, 1];
};

/**
 * Create a smooth easing curve
 * @returns {array} Cubic bezier easing values
 */
export const smoothEasing = () => [0.25, 0.46, 0.45, 0.94];

/**
 * Create a snappy easing curve
 * @returns {array} Cubic bezier easing values
 */
export const snappyEasing = () => [0.68, -0.55, 0.265, 1.55];

/**
 * Convert CSS animation to Framer Motion variant
 * @param {string} cssAnimation - CSS animation keyframes name
 * @returns {object} Framer Motion variant object
 */
export const cssToFramerVariant = (cssAnimation) => {
    return {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                duration: 0.5,
            },
        },
    };
};

/**
 * Create a color transition animation
 * @param {array} colors - Array of colors to transition through
 * @param {number} duration - Total animation duration
 * @returns {object} Framer Motion variant
 */
export const colorTransitionVariant = (colors = ['#fbbf24', '#3b82f6'], duration = 2) => {
    return {
        animate: {
            backgroundColor: colors,
            transition: {
                duration,
                repeat: Infinity,
                repeatType: 'reverse',
            },
        },
    };
};

/**
 * Create a rotation animation
 * @param {number} degrees - Rotation degrees
 * @param {number} duration - Duration in seconds
 * @returns {object} Framer Motion variant
 */
export const rotationVariant = (degrees = 360, duration = 2) => {
    return {
        animate: {
            rotate: degrees,
            transition: {
                duration,
                repeat: Infinity,
                ease: 'linear',
            },
        },
    };
};

/**
 * Create a scale pulse animation
 * @param {number} scale - Scale factor (e.g., 1.1 for 110%)
 * @param {number} duration - Duration in seconds
 * @returns {object} Framer Motion variant
 */
export const scalePulseVariant = (scale = 1.1, duration = 1) => {
    return {
        animate: {
            scale: [1, scale, 1],
            transition: {
                duration,
                repeat: Infinity,
                ease: 'easeInOut',
            },
        },
    };
};

/**
 * Create a position animation (movement)
 * @param {object} startPos - Starting position {x, y}
 * @param {object} endPos - Ending position {x, y}
 * @param {number} duration - Duration in seconds
 * @returns {object} Framer Motion variant
 */
export const positionVariant = (startPos = { x: 0, y: 0 }, endPos = { x: 100, y: 0 }, duration = 2) => {
    return {
        initial: startPos,
        animate: {
            ...endPos,
            transition: {
                duration,
                repeat: Infinity,
                repeatType: 'reverse',
            },
        },
    };
};

/**
 * Create a parallax effect with different speeds
 * @param {number} offset - Scroll offset multiplier
 * @returns {object} Motion values
 */
export const parallaxVariant = (offset = 0.5) => {
    return {
        y: typeof window !== 'undefined' ? window.scrollY * offset : 0,
    };
};

/**
 * Create a wave animation
 * @param {number} delay - Delay between items
 * @param {number} index - Item index
 * @returns {number} Calculated animation delay
 */
export const waveDelay = (index, delay = 0.1) => index * delay;

/**
 * Combine multiple easing functions
 * @param {...array} easings - Easing functions to combine
 * @returns {array} Combined easing
 */
export const combineEasings = (...easings) => {
    // This would require more complex implementation
    // For now, return the first easing
    return easings[0] || [0.25, 0.46, 0.45, 0.94];
};

/**
 * Create a responsive animation based on viewport
 * @param {string} viewport - 'mobile', 'tablet', 'desktop'
 * @returns {number} Duration based on viewport
 */
export const responsiveAnimationDuration = (viewport = 'desktop') => {
    const durations = {
        mobile: 0.3,
        tablet: 0.4,
        desktop: 0.5,
    };
    return durations[viewport] || durations.desktop;
};

/**
 * Create a spring animation config
 * @param {string} type - 'gentle', 'normal', 'bouncy', 'stiff'
 * @returns {object} Spring config
 */
export const springConfig = (type = 'normal') => {
    const configs = {
        gentle: {
            type: 'spring',
            stiffness: 50,
            damping: 20,
            mass: 1,
        },
        normal: {
            type: 'spring',
            stiffness: 100,
            damping: 10,
            mass: 1,
        },
        bouncy: {
            type: 'spring',
            stiffness: 300,
            damping: 10,
            mass: 0.5,
        },
        stiff: {
            type: 'spring',
            stiffness: 500,
            damping: 50,
            mass: 1,
        },
    };
    return configs[type] || configs.normal;
};

/**
 * Create animation sequence for multiple elements
 * @param {number} count - Number of elements
 * @param {number} baseDelay - Base delay
 * @returns {array} Array of delays
 */
export const createSequence = (count = 5, baseDelay = 0.1) => {
    return Array.from({ length: count }, (_, i) => baseDelay + i * 0.1);
};

/**
 * Get animation performance mode (reduce motion for accessibility)
 * @returns {boolean} Should reduce motion
 */
export const shouldReduceMotion = () => {
    if (typeof window === 'undefined') return false;
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
};

/**
 * Create accessible animation variant
 * Respects prefers-reduced-motion
 * @param {object} variant - Original variant
 * @returns {object} Accessible variant
 */
export const createAccessibleVariant = (variant) => {
    if (shouldReduceMotion()) {
        return {
            ...variant,
            transition: {
                ...variant.transition,
                duration: 0.01,
            },
        };
    }
    return variant;
};

/**
 * Interpolate between values
 * @param {number} start - Start value
 * @param {number} end - End value
 * @param {number} progress - Progress (0-1)
 * @returns {number} Interpolated value
 */
export const interpolate = (start, end, progress) => {
    return start + (end - start) * progress;
};

/**
 * Clamp value between min and max
 * @param {number} value - Value to clamp
 * @param {number} min - Minimum value
 * @param {number} max - Maximum value
 * @returns {number} Clamped value
 */
export const clamp = (value, min, max) => {
    return Math.min(Math.max(value, min), max);
};

/**
 * Map value from one range to another
 * @param {number} value - Value to map
 * @param {number} inMin - Input minimum
 * @param {number} inMax - Input maximum
 * @param {number} outMin - Output minimum
 * @param {number} outMax - Output maximum
 * @returns {number} Mapped value
 */
export const mapRange = (value, inMin, inMax, outMin, outMax) => {
    return ((value - inMin) / (inMax - inMin)) * (outMax - outMin) + outMin;
};

/**
 * Create a throttle function for animation frames
 * @param {function} callback - Function to throttle
 * @param {number} limit - Time limit in ms
 * @returns {function} Throttled function
 */
export const throttleAnimationFrame = (callback, limit = 16) => {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            callback.apply(this, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
};

/**
 * Check if element is in viewport
 * @param {HTMLElement} element - Element to check
 * @returns {boolean} Is in viewport
 */
export const isInViewport = (element) => {
    if (!element) return false;
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
};

/**
 * Get scroll progress (0-1)
 * @returns {number} Scroll progress
 */
export const getScrollProgress = () => {
    const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
    return window.scrollY / scrollHeight;
};

export default {
    createStaggerDelay,
    randomDelay,
    createBounceEasing,
    smoothEasing,
    snappyEasing,
    cssToFramerVariant,
    colorTransitionVariant,
    rotationVariant,
    scalePulseVariant,
    positionVariant,
    parallaxVariant,
    waveDelay,
    combineEasings,
    responsiveAnimationDuration,
    springConfig,
    createSequence,
    shouldReduceMotion,
    createAccessibleVariant,
    interpolate,
    clamp,
    mapRange,
    throttleAnimationFrame,
    isInViewport,
    getScrollProgress,
};
