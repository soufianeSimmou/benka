/**
 * AnimationShowcase Component
 * Demonstrates all Framer Motion animation possibilities
 * This is a reference/documentation component
 */

import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import useAnimationVariants from '../hooks/useAnimationVariants';

const AnimationShowcase = () => {
    const variants = useAnimationVariants();
    const [selectedAnimation, setSelectedAnimation] = React.useState('card');

    const animationExamples = {
        card: {
            name: 'Card Entrance',
            variants: variants.cardVariants,
            description: 'Cards scale up and fade in with bounce',
        },
        slide: {
            name: 'Slide from Left',
            variants: variants.slideFromLeftVariants,
            description: 'Slide in animation from the left',
        },
        bounce: {
            name: 'Bounce In',
            variants: variants.bounceInVariants,
            description: 'Bouncy entrance animation',
        },
        flip: {
            name: 'Flip In',
            variants: variants.flipVariants,
            description: '3D flip entrance animation',
        },
        rotate: {
            name: 'Rotate In',
            variants: variants.rotateInVariants,
            description: 'Rotate with scale entrance',
        },
    };

    return (
        <div style={{ padding: '2rem', backgroundColor: '#f9fafb', minHeight: '100vh' }}>
            <h1 style={{
                fontSize: '2.5rem',
                fontWeight: 'bold',
                marginBottom: '2rem',
                background: 'linear-gradient(135deg, #3b82f6 0%, #fbbf24 100%)',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                backgroundClip: 'text',
            }}>
                Framer Motion Animation Showcase
            </h1>

            {/* Animation Selector */}
            <div style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))',
                gap: '1rem',
                marginBottom: '3rem',
            }}>
                {Object.entries(animationExamples).map(([key, { name }]) => (
                    <motion.button
                        key={key}
                        onClick={() => setSelectedAnimation(key)}
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        style={{
                            padding: '1rem',
                            border: selectedAnimation === key ? '2px solid #3b82f6' : '2px solid #e5e7eb',
                            borderRadius: '0.5rem',
                            backgroundColor: selectedAnimation === key ? '#eff6ff' : '#ffffff',
                            cursor: 'pointer',
                            fontWeight: 600,
                            color: selectedAnimation === key ? '#1e40af' : '#6b7280',
                        }}
                    >
                        {name}
                    </motion.button>
                ))}
            </div>

            {/* Animation Preview */}
            <div style={{
                backgroundColor: '#ffffff',
                borderRadius: '1rem',
                padding: '4rem 2rem',
                boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
                minHeight: '400px',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
            }}>
                <p style={{
                    marginBottom: '2rem',
                    color: '#6b7280',
                    fontSize: '1rem',
                }}>
                    {animationExamples[selectedAnimation].description}
                </p>

                <AnimatePresence mode="wait">
                    <motion.div
                        key={selectedAnimation}
                        variants={animationExamples[selectedAnimation].variants}
                        initial="hidden"
                        animate="visible"
                        exit="exit"
                        whileHover="hover"
                        style={{
                            width: '200px',
                            height: '200px',
                            borderRadius: '1rem',
                            background: 'linear-gradient(135deg, #fbbf24 0%, #3b82f6 100%)',
                            boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.1)',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: '#ffffff',
                            fontSize: '2rem',
                            fontWeight: 'bold',
                        }}
                    >
                        ✨
                    </motion.div>
                </AnimatePresence>

                <p style={{
                    marginTop: '2rem',
                    color: '#9ca3af',
                    fontSize: '0.875rem',
                }}>
                    Hover over the box to see additional effects
                </p>
            </div>

            {/* Code Example */}
            <div style={{
                marginTop: '3rem',
                backgroundColor: '#1f2937',
                borderRadius: '1rem',
                padding: '1.5rem',
                overflow: 'auto',
            }}>
                <p style={{ color: '#fbbf24', fontWeight: 'bold', margin: '0 0 1rem 0' }}>
                    Code Example:
                </p>
                <pre style={{
                    color: '#e5e7eb',
                    fontSize: '0.875rem',
                    lineHeight: '1.5',
                    margin: 0,
                    fontFamily: 'monospace',
                }}>
{`<motion.div
  variants={variants.${selectedAnimation.includes('slide') ? 'slideFromLeftVariants' : selectedAnimation === 'card' ? 'cardVariants' : selectedAnimation + 'Variants'}}
  initial="hidden"
  animate="visible"
  whileHover="hover"
>
  Content
</motion.div>`}
                </pre>
            </div>

            {/* Useful Information */}
            <div style={{
                marginTop: '3rem',
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
                gap: '2rem',
            }}>
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.1 }}
                    style={{
                        backgroundColor: '#ffffff',
                        borderRadius: '1rem',
                        padding: '2rem',
                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                    }}
                >
                    <h3 style={{ color: '#3b82f6', fontWeight: 'bold', marginTop: 0 }}>Timing</h3>
                    <ul style={{ color: '#6b7280', paddingLeft: '1.5rem' }}>
                        <li>Fast: 0.2s</li>
                        <li>Normal: 0.3s</li>
                        <li>Slow: 0.5s</li>
                        <li>Slowest: 0.8s</li>
                    </ul>
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.2 }}
                    style={{
                        backgroundColor: '#ffffff',
                        borderRadius: '1rem',
                        padding: '2rem',
                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                    }}
                >
                    <h3 style={{ color: '#fbbf24', fontWeight: 'bold', marginTop: 0 }}>Easing Functions</h3>
                    <ul style={{ color: '#6b7280', paddingLeft: '1.5rem' }}>
                        <li>easeOut</li>
                        <li>easeInOut</li>
                        <li>cubic-bezier</li>
                        <li>Spring physics</li>
                    </ul>
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.3 }}
                    style={{
                        backgroundColor: '#ffffff',
                        borderRadius: '1rem',
                        padding: '2rem',
                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                    }}
                >
                    <h3 style={{ color: '#22c55e', fontWeight: 'bold', marginTop: 0 }}>Interactions</h3>
                    <ul style={{ color: '#6b7280', paddingLeft: '1.5rem' }}>
                        <li>whileHover</li>
                        <li>whileTap</li>
                        <li>whileFocus</li>
                        <li>AnimatePresence</li>
                    </ul>
                </motion.div>
            </div>
        </div>
    );
};

export default AnimationShowcase;
