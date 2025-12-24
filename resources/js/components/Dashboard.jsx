import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const Dashboard = () => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [activeSection, setActiveSection] = useState('overview');

    useEffect(() => {
        loadStatistics();
    }, []);

    const loadStatistics = async () => {
        try {
            setLoading(true);
            const response = await fetch('/api/statistics/monthly', {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            setData(result);
            setError(null);
        } catch (err) {
            console.error('Error loading statistics:', err);
            setError('Erreur lors du chargement des statistiques');
        } finally {
            setLoading(false);
        }
    };

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: { staggerChildren: 0.1, delayChildren: 0.1 }
        }
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 20 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { duration: 0.5, ease: [0.25, 0.46, 0.45, 0.94] }
        }
    };

    const cardVariants = {
        hidden: { opacity: 0, scale: 0.9, y: 20 },
        visible: {
            opacity: 1,
            scale: 1,
            y: 0,
            transition: { duration: 0.4, ease: [0.34, 1.56, 0.64, 1] }
        },
        hover: {
            y: -4,
            scale: 1.02,
            transition: { duration: 0.2 }
        }
    };

    const AnimatedNumber = ({ value, suffix = '' }) => {
        const [displayValue, setDisplayValue] = useState(0);

        useEffect(() => {
            if (value !== undefined && value !== null) {
                const numValue = parseFloat(value) || 0;
                let start = 0;
                const duration = 800;
                const increment = numValue / (duration / 16);
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= numValue) {
                        setDisplayValue(numValue);
                        clearInterval(timer);
                    } else {
                        setDisplayValue(Math.floor(start));
                    }
                }, 16);
                return () => clearInterval(timer);
            }
        }, [value]);

        return <>{displayValue}{suffix}</>;
    };

    if (loading) {
        return (
            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-yellow-50">
                <div className="max-w-lg mx-auto px-4 py-6">
                    <div className="animate-pulse space-y-4">
                        <div className="h-8 bg-gray-200 rounded-xl w-3/4"></div>
                        <div className="h-24 bg-gray-200 rounded-2xl"></div>
                        <div className="grid grid-cols-2 gap-3">
                            <div className="h-20 bg-gray-200 rounded-xl"></div>
                            <div className="h-20 bg-gray-200 rounded-xl"></div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    if (error || !data) {
        return (
            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-yellow-50 flex items-center justify-center">
                <div className="text-center px-4">
                    <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg className="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <p className="text-gray-600">{error || 'Erreur de chargement'}</p>
                </div>
            </div>
        );
    }

    const { employees = [], summary = {} } = data;

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-yellow-50">
            {/* Background decorations */}
            <div className="fixed inset-0 overflow-hidden pointer-events-none">
                <div className="absolute -top-40 -right-40 w-80 h-80 bg-blue-200 rounded-full opacity-20 blur-3xl"></div>
                <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-yellow-200 rounded-full opacity-20 blur-3xl"></div>
            </div>

            <motion.div
                className="relative max-w-lg mx-auto px-4 py-6 pb-24"
                initial="hidden"
                animate="visible"
                variants={containerVariants}
            >
                {/* Header */}
                <motion.div variants={itemVariants} className="mb-6">
                    <h1 className="text-2xl font-bold text-gray-900">Statistiques</h1>
                    <p className="text-gray-500 text-sm mt-1">{data.month} {data.year}</p>
                </motion.div>

                {/* Section Tabs */}
                <motion.div variants={itemVariants} className="flex gap-2 mb-6">
                    {['overview', 'employees'].map((section) => (
                        <motion.button
                            key={section}
                            onClick={() => setActiveSection(section)}
                            className={`flex-1 py-2.5 px-4 rounded-xl text-sm font-medium transition-all duration-300 ${
                                activeSection === section
                                    ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30'
                                    : 'bg-white text-gray-600 hover:bg-gray-50'
                            }`}
                            whileTap={{ scale: 0.98 }}
                        >
                            {section === 'overview' ? 'Vue globale' : 'Par employe'}
                        </motion.button>
                    ))}
                </motion.div>

                <AnimatePresence mode="wait">
                    {activeSection === 'overview' ? (
                        <motion.div
                            key="overview"
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: 20 }}
                            transition={{ duration: 0.3 }}
                            className="space-y-4"
                        >
                            {/* Main Stats Card */}
                            <motion.div
                                variants={cardVariants}
                                whileHover="hover"
                                className="bg-white rounded-2xl p-5 shadow-sm border border-gray-100"
                            >
                                <div className="flex items-center justify-between mb-4">
                                    <div>
                                        <p className="text-gray-500 text-sm">Taux de presence global</p>
                                        <p className="text-4xl font-bold text-blue-500 mt-1">
                                            <AnimatedNumber value={parseInt(summary.overall_rate || 0)} suffix="%" />
                                        </p>
                                    </div>
                                    <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                        <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                </div>
                                {/* Progress bar */}
                                <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <motion.div
                                        className="h-full bg-gradient-to-r from-blue-500 to-blue-400 rounded-full"
                                        initial={{ width: 0 }}
                                        animate={{ width: `${summary.overall_rate || 0}%` }}
                                        transition={{ duration: 1, delay: 0.3, ease: "easeOut" }}
                                    />
                                </div>
                            </motion.div>

                            {/* KPI Grid */}
                            <div className="grid grid-cols-2 gap-3">
                                {/* Employes actifs */}
                                <motion.div
                                    variants={cardVariants}
                                    whileHover="hover"
                                    className="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                                >
                                    <div className="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center mb-3">
                                        <svg className="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p className="text-2xl font-bold text-gray-900">
                                        <AnimatedNumber value={summary.total_employees || 0} />
                                    </p>
                                    <p className="text-xs text-gray-500 mt-1">Employes actifs</p>
                                </motion.div>

                                {/* Jours travailles */}
                                <motion.div
                                    variants={cardVariants}
                                    whileHover="hover"
                                    className="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                                >
                                    <div className="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                                        <svg className="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p className="text-2xl font-bold text-emerald-600">
                                        <AnimatedNumber value={summary.total_present || 0} />
                                    </p>
                                    <p className="text-xs text-gray-500 mt-1">Jours travailles</p>
                                </motion.div>

                                {/* Jours absents */}
                                <motion.div
                                    variants={cardVariants}
                                    whileHover="hover"
                                    className="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                                >
                                    <div className="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
                                        <svg className="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p className="text-2xl font-bold text-red-500">
                                        <AnimatedNumber value={summary.total_absent || 0} />
                                    </p>
                                    <p className="text-xs text-gray-500 mt-1">Jours absents</p>
                                </motion.div>

                                {/* Total jours */}
                                <motion.div
                                    variants={cardVariants}
                                    whileHover="hover"
                                    className="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                                >
                                    <div className="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                        <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p className="text-2xl font-bold text-blue-600">
                                        <AnimatedNumber value={(summary.total_present || 0) + (summary.total_absent || 0)} />
                                    </p>
                                    <p className="text-xs text-gray-500 mt-1">Total jours</p>
                                </motion.div>
                            </div>
                        </motion.div>
                    ) : (
                        <motion.div
                            key="employees"
                            initial={{ opacity: 0, x: 20 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: -20 }}
                            transition={{ duration: 0.3 }}
                            className="space-y-3"
                        >
                            {employees.length > 0 ? (
                                employees
                                    .sort((a, b) => (b.attendance_rate || 0) - (a.attendance_rate || 0))
                                    .map((employee, index) => (
                                        <motion.div
                                            key={employee.id || index}
                                            initial={{ opacity: 0, y: 20 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            transition={{ delay: index * 0.05 }}
                                            whileHover={{ y: -2, boxShadow: '0 8px 25px rgba(0,0,0,0.1)' }}
                                            className="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                                        >
                                            <div className="flex items-start justify-between mb-3">
                                                <div>
                                                    <h4 className="font-semibold text-gray-900">{employee.name}</h4>
                                                    <p className="text-sm text-blue-500">{employee.job_role || 'Non defini'}</p>
                                                </div>
                                                <motion.div
                                                    initial={{ scale: 0 }}
                                                    animate={{ scale: 1 }}
                                                    transition={{ delay: 0.2 + index * 0.05, type: "spring" }}
                                                    className={`px-3 py-1 rounded-full text-sm font-bold ${
                                                        (employee.attendance_rate || 0) >= 80
                                                            ? 'bg-emerald-100 text-emerald-700'
                                                            : (employee.attendance_rate || 0) >= 50
                                                            ? 'bg-yellow-100 text-yellow-700'
                                                            : 'bg-red-100 text-red-600'
                                                    }`}
                                                >
                                                    {employee.attendance_rate || 0}%
                                                </motion.div>
                                            </div>

                                            <div className="grid grid-cols-3 gap-2 mb-3">
                                                <div className="bg-emerald-50 rounded-lg p-2 text-center">
                                                    <p className="text-lg font-bold text-emerald-600">{employee.present_days || 0}</p>
                                                    <p className="text-[10px] text-emerald-600 uppercase">Present</p>
                                                </div>
                                                <div className="bg-red-50 rounded-lg p-2 text-center">
                                                    <p className="text-lg font-bold text-red-500">{employee.absent_days || 0}</p>
                                                    <p className="text-[10px] text-red-500 uppercase">Absent</p>
                                                </div>
                                                <div className="bg-gray-50 rounded-lg p-2 text-center">
                                                    <p className="text-lg font-bold text-gray-700">{employee.total_days || 0}</p>
                                                    <p className="text-[10px] text-gray-500 uppercase">Total</p>
                                                </div>
                                            </div>

                                            {/* Progress bar */}
                                            <div className="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <motion.div
                                                    className={`h-full rounded-full ${
                                                        (employee.attendance_rate || 0) >= 80
                                                            ? 'bg-gradient-to-r from-emerald-500 to-emerald-400'
                                                            : (employee.attendance_rate || 0) >= 50
                                                            ? 'bg-gradient-to-r from-yellow-500 to-yellow-400'
                                                            : 'bg-gradient-to-r from-red-500 to-red-400'
                                                    }`}
                                                    initial={{ width: 0 }}
                                                    animate={{ width: `${employee.attendance_rate || 0}%` }}
                                                    transition={{ duration: 0.8, delay: 0.3 + index * 0.05 }}
                                                />
                                            </div>
                                        </motion.div>
                                    ))
                            ) : (
                                <div className="text-center py-12">
                                    <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p className="text-gray-500">Aucun employe</p>
                                </div>
                            )}
                        </motion.div>
                    )}
                </AnimatePresence>
            </motion.div>
        </div>
    );
};

export default Dashboard;
