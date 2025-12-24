/**
 * Custom Hook: useAnimationVariants
 * Provides reusable Framer Motion animation variants
 */

export const useAnimationVariants = () => {
    // Container Animations
    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: 0.1,
                delayChildren: 0.2,
            },
        },
        exit: {
            opacity: 0,
            transition: {
                staggerChildren: 0.05,
                staggerDirection: -1,
            },
        },
    };

    // Item Animations
    const itemVariants = {
        hidden: { opacity: 0, y: 20 },
        visible: {
            opacity: 1,
            y: 0,
            transition: {
                duration: 0.5,
                ease: 'easeOut',
            },
        },
        exit: {
            opacity: 0,
            y: -20,
            transition: { duration: 0.3 },
        },
    };

    // Card Animations with Bounce
    const cardVariants = {
        hidden: { opacity: 0, scale: 0.8, y: 20 },
        visible: {
            opacity: 1,
            scale: 1,
            y: 0,
            transition: {
                duration: 0.4,
                ease: [0.34, 1.56, 0.64, 1], // Elastic easing
            },
        },
        hover: {
            y: -8,
            boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.1)',
            transition: { duration: 0.3, ease: 'easeOut' },
        },
        tap: {
            scale: 0.98,
        },
    };

    // Slide from Left
    const slideFromLeftVariants = {
        hidden: { opacity: 0, x: -50 },
        visible: {
            opacity: 1,
            x: 0,
            transition: {
                duration: 0.5,
                ease: 'easeOut',
            },
        },
    };

    // Slide from Right
    const slideFromRightVariants = {
        hidden: { opacity: 0, x: 50 },
        visible: {
            opacity: 1,
            x: 0,
            transition: {
                duration: 0.5,
                ease: 'easeOut',
            },
        },
    };

    // Rotate In
    const rotateInVariants = {
        hidden: { opacity: 0, rotate: -10, scale: 0.9 },
        visible: {
            opacity: 1,
            rotate: 0,
            scale: 1,
            transition: {
                duration: 0.6,
                ease: [0.34, 1.56, 0.64, 1],
            },
        },
    };

    // Expand/Collapse
    const expandVariants = {
        hidden: { opacity: 0, scale: 0.5, height: 0 },
        visible: {
            opacity: 1,
            scale: 1,
            height: 'auto',
            transition: {
                duration: 0.4,
                ease: 'easeOut',
            },
        },
        exit: {
            opacity: 0,
            scale: 0.5,
            height: 0,
            transition: {
                duration: 0.3,
                ease: 'easeIn',
            },
        },
    };

    // Fade In/Out
    const fadeVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: { duration: 0.3 },
        },
        exit: {
            opacity: 0,
            transition: { duration: 0.3 },
        },
    };

    // Bounce In
    const bounceInVariants = {
        hidden: { opacity: 0, scale: 0.3, y: -50 },
        visible: {
            opacity: 1,
            scale: 1,
            y: 0,
            transition: {
                duration: 0.6,
                ease: [0.68, -0.55, 0.265, 1.55], // Bounce easing
            },
        },
    };

    // Flip
    const flipVariants = {
        hidden: { opacity: 0, rotateY: -90 },
        visible: {
            opacity: 1,
            rotateY: 0,
            transition: {
                duration: 0.6,
                ease: 'easeOut',
            },
        },
    };

    // Progress Bar
    const progressBarVariants = {
        hidden: { width: 0, opacity: 0 },
        visible: (width) => ({
            width: `${width}%`,
            opacity: 1,
            transition: {
                duration: 0.8,
                ease: 'easeOut',
            },
        }),
    };

    // Number Counter
    const numberVariants = {
        hidden: { opacity: 0, y: 10 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { duration: 0.5 },
        },
    };

    // Badge/Tag
    const badgeVariants = {
        hidden: { opacity: 0, scale: 0.5, y: -10 },
        visible: {
            opacity: 1,
            scale: 1,
            y: 0,
            transition: {
                duration: 0.4,
                ease: [0.34, 1.56, 0.64, 1],
            },
        },
    };

    // Pulse Animation (for active elements)
    const pulseVariants = {
        hidden: { opacity: 0.5 },
        visible: {
            opacity: 1,
            transition: {
                duration: 1,
                repeat: Infinity,
                repeatType: 'reverse',
            },
        },
    };

    // Shimmer Animation (for loading)
    const shimmerVariants = {
        hidden: { backgroundPosition: '-1000px 0' },
        visible: {
            backgroundPosition: '1000px 0',
            transition: {
                duration: 2,
                repeat: Infinity,
                ease: 'linear',
            },
        },
    };

    // Hover Lift Animation
    const hoverLiftVariants = {
        rest: {
            y: 0,
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
        },
        hover: {
            y: -8,
            boxShadow: '0 20px 25px rgba(0, 0, 0, 0.15)',
        },
    };

    // Text Reveal
    const textRevealVariants = {
        hidden: {
            opacity: 0,
            y: 10,
        },
        visible: (i) => ({
            opacity: 1,
            y: 0,
            transition: {
                delay: i * 0.1,
                duration: 0.5,
                ease: 'easeOut',
            },
        }),
    };

    // Spring Animation Config
    const springConfig = {
        default: {
            type: 'spring',
            stiffness: 100,
            damping: 10,
        },
        bouncy: {
            type: 'spring',
            stiffness: 300,
            damping: 10,
        },
        smooth: {
            type: 'spring',
            stiffness: 50,
            damping: 20,
        },
        snappy: {
            type: 'spring',
            stiffness: 200,
            damping: 20,
        },
    };

    // Easing Functions
    const easingFunctions = {
        linear: 'linear',
        ease: 'ease',
        easeIn: 'ease-in',
        easeOut: 'ease-out',
        easeInOut: 'ease-in-out',
        cubic: [0.4, 0, 0.2, 1],
        elasticOut: [0.34, 1.56, 0.64, 1],
        bounce: [0.68, -0.55, 0.265, 1.55],
        easeInBack: [0.6, -0.28, 0.735, 0.045],
        easeOutBack: [0.175, 0.885, 0.32, 1.275],
    };

    // Transition Templates
    const transitions = {
        fast: { duration: 0.2 },
        normal: { duration: 0.3 },
        slow: { duration: 0.5 },
        slowest: { duration: 0.8 },
        withBounce: {
            type: 'spring',
            bounce: 0.5,
            duration: 0.8,
        },
        smooth: {
            type: 'tween',
            ease: 'easeInOut',
            duration: 0.4,
        },
    };

    return {
        // Variants
        containerVariants,
        itemVariants,
        cardVariants,
        slideFromLeftVariants,
        slideFromRightVariants,
        rotateInVariants,
        expandVariants,
        fadeVariants,
        bounceInVariants,
        flipVariants,
        progressBarVariants,
        numberVariants,
        badgeVariants,
        pulseVariants,
        shimmerVariants,
        hoverLiftVariants,
        textRevealVariants,
        // Configs
        springConfig,
        easingFunctions,
        transitions,
    };
};

export default useAnimationVariants;
