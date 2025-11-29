import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { SkeletonPaymentMethods } from '../components/SkeletonLoader';
import { PageHeader, Section, Grid, Icon } from '../components/DesignSystem';

const PaymentMethodsPage: React.FC = () => {
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Simulate loading
    const timer = setTimeout(() => {
      setLoading(false);
    }, 1000);
    return () => clearTimeout(timer);
  }, []);

  const paymentMethods = [
    {
      name: 'DANA',
      number: '081234567890',
      logo: (
        <svg viewBox="0 0 200 60" className="w-12 h-8">
          <defs>
            <linearGradient id="dana-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stopColor="#118EEA" />
              <stop offset="100%" stopColor="#0D6EFD" />
            </linearGradient>
          </defs>
          {/* DANA Logo - Simplified version */}
          <circle cx="30" cy="30" r="25" fill="url(#dana-gradient)"/>
          <text x="30" y="35" textAnchor="middle" fill="white" fontSize="16" fontWeight="bold">D</text>
          <text x="70" y="35" fill="url(#dana-gradient)" fontSize="18" fontWeight="bold">DANA</text>
        </svg>
      ),
      theme: {
        bg: 'bg-gradient-to-br from-blue-500 to-blue-600',
        text: 'text-white',
        icon: 'text-blue-100',
        border: 'border-blue-400',
        hover: 'hover:from-blue-600 hover:to-blue-700',
        shadow: 'shadow-lg shadow-blue-500/25'
      }
    },
    {
      name: 'Bank Mandiri',
      number: '1234567890',
      logo: (
        <svg viewBox="0 0 200 60" className="w-12 h-8">
          <defs>
            <linearGradient id="mandiri-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stopColor="#E53E3E" />
              <stop offset="100%" stopColor="#C53030" />
            </linearGradient>
          </defs>
          {/* Mandiri Logo - Simplified version */}
          <rect x="5" y="10" width="40" height="40" rx="8" fill="url(#mandiri-gradient)"/>
          <text x="25" y="35" textAnchor="middle" fill="white" fontSize="12" fontWeight="bold">M</text>
          <text x="70" y="35" fill="url(#mandiri-gradient)" fontSize="16" fontWeight="bold">MANDIRI</text>
        </svg>
      ),
      theme: {
        bg: 'bg-gradient-to-br from-red-500 to-red-600',
        text: 'text-white',
        icon: 'text-red-100',
        border: 'border-red-400',
        hover: 'hover:from-red-600 hover:to-red-700',
        shadow: 'shadow-lg shadow-red-500/25'
      }
    }
  ];

  if (loading) {
    return <SkeletonPaymentMethods />;
  }

  return (
    <div className="space-y-6">
        {/* Header */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
        >
          <PageHeader 
            title="Metode Pembayaran"
            subtitle="Pilih metode pembayaran yang tersedia untuk melakukan pembayaran tagihan"
          />
        </motion.div>

        {/* Payment Methods Grid */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.1 }}
        >
          <Grid cols={2} gap="sm">
            {paymentMethods.map((method, index) => (
              <motion.div
                key={method.name}
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.3, delay: 0.2 + (index * 0.1) }}
              >
                <div className={`
                  relative overflow-hidden rounded-2xl border-2 cursor-pointer
                  transition-all duration-300 transform hover:scale-105
                  ${method.theme.bg} ${method.theme.border} ${method.theme.hover} ${method.theme.shadow}
                `}>
                  <div className="p-6">
                    <div className="flex flex-col items-center text-center">
                      <div className="mb-4 p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        {method.logo}
                      </div>
                      <h3 className={`text-lg font-bold ${method.theme.text} mb-2`}>
                        {method.name}
                      </h3>
                      <p className={`text-sm ${method.theme.icon} mb-3`}>
                        {method.number}
                      </p>
                      <div className={`
                        px-4 py-2 rounded-lg text-xs font-medium
                        bg-white/20 backdrop-blur-sm ${method.theme.text}
                        border border-white/30
                      `}>
                        Transfer Sekarang
                      </div>
                    </div>
                  </div>
                  
                  {/* Decorative elements */}
                  <div className="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-10 translate-x-10"></div>
                  <div className="absolute bottom-0 left-0 w-16 h-16 bg-white/5 rounded-full translate-y-8 -translate-x-8"></div>
                </div>
              </motion.div>
            ))}
          </Grid>
        </motion.div>

        {/* Info Card */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.3 }}
        >
          <Section title="Cara Pembayaran" variant="primary">
            <div className="flex items-start">
              <div className="flex-shrink-0">
                <div className="h-10 w-10 bg-blue-50 rounded-xl flex items-center justify-center">
                  <Icon color="primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </Icon>
                </div>
              </div>
              <div className="ml-4">
                <div className="space-y-3 text-sm text-gray-600">
                  <div className="flex items-start">
                    <div className="h-2 w-2 bg-blue-500 rounded-full mr-3 mt-2"></div>
                    <span>Transfer sesuai nominal tagihan yang tertera</span>
                  </div>
                  <div className="flex items-start">
                    <div className="h-2 w-2 bg-blue-500 rounded-full mr-3 mt-2"></div>
                    <span>Upload bukti pembayaran setelah transfer</span>
                  </div>
                  <div className="flex items-start">
                    <div className="h-2 w-2 bg-blue-500 rounded-full mr-3 mt-2"></div>
                    <span>Pembayaran akan diproses dalam 1x24 jam</span>
                  </div>
                  <div className="flex items-start">
                    <div className="h-2 w-2 bg-blue-500 rounded-full mr-3 mt-2"></div>
                    <span>Pastikan nomor rekening/akun sudah benar</span>
                  </div>
                </div>
              </div>
            </div>
          </Section>
        </motion.div>
    </div>
  );
};

export default PaymentMethodsPage;