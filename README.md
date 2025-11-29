# Customer Portal - React Frontend

Customer Self-Service Portal untuk sistem WiFi Billing Management.

## 🚀 Features

- **Customer Authentication** - Login dengan nomor HP atau PPPoE
- **Payment Management** - Lihat tagihan dan upload bukti pembayaran
- **Ticket System** - Buat dan kelola support tickets
- **Profile Management** - Update informasi customer
- **Responsive Design** - Mobile-friendly interface

## 🛠️ Tech Stack

- **React 18** - Frontend framework
- **TypeScript** - Type safety
- **Tailwind CSS** - Styling
- **Axios** - HTTP client
- **React Router** - Navigation
- **Heroicons** - Icons

## 📦 Installation

1. Install dependencies:
```bash
npm install
```

2. Create environment file:
```bash
# Create .env file
REACT_APP_API_URL=http://localhost:8000/api/v1
```

3. Start development server:
```bash
npm start
```

## 🔧 Configuration

### API Configuration
Update `src/config/api.ts` untuk mengubah API endpoint:

```typescript
export const API_CONFIG = {
  BASE_URL: 'http://your-backend-url.com/api/v1',
  TIMEOUT: 10000,
};
```

### Environment Variables
- `REACT_APP_API_URL` - Backend API URL

## 📱 Usage

### Customer Login
Customer dapat login menggunakan:
- **Nomor HP** (contoh: 081234567890)
- **PPPoE Username** (contoh: customer123)

**Password Default:** 6 digit terakhir nomor HP

### Features

#### Dashboard
- Overview customer information
- Statistics (payments, tickets)
- Unpaid bills notification
- Quick actions

#### Payment Management
- View unpaid bills
- Upload payment proof
- Payment history
- Send proof via WhatsApp

#### Ticket System
- Create support tickets
- View ticket status
- Add comments
- Upload attachments
- Rate resolution

#### Profile Management
- Update customer information
- Change password
- View statistics

## 🏗️ Project Structure

```
src/
├── components/          # Reusable components
│   ├── Layout.tsx      # Main layout component
│   └── LoginForm.tsx   # Login form component
├── contexts/           # React contexts
│   └── AuthContext.tsx # Authentication context
├── pages/              # Page components
│   └── Dashboard.tsx   # Dashboard page
├── services/           # API services
│   └── api.ts         # API client
├── types/              # TypeScript types
│   └── index.ts       # Type definitions
├── config/             # Configuration
│   └── api.ts         # API configuration
└── App.tsx            # Main app component
```

## 🔐 Authentication

Sistem menggunakan Laravel Sanctum untuk authentication:

1. Customer login dengan username (HP/PPPoE) dan password
2. Backend mengembalikan token
3. Token disimpan di localStorage
4. Token digunakan untuk semua API requests
5. Auto logout jika token expired

## 📱 Mobile Support

- Responsive design untuk semua screen sizes
- Touch-friendly interface
- Mobile-optimized forms
- PWA ready

## 🚀 Deployment

### Build for Production
```bash
npm run build
```

### Deploy to Static Hosting
Build files akan tersedia di `build/` directory.

### Environment Configuration
Pastikan set environment variables untuk production:
- `REACT_APP_API_URL` - Production API URL

## 🔧 Development

### Available Scripts
- `npm start` - Start development server
- `npm run build` - Build for production
- `npm test` - Run tests
- `npm run eject` - Eject from Create React App

### Code Style
- TypeScript untuk type safety
- ESLint untuk code quality
- Prettier untuk code formatting

## 📚 API Integration

Frontend terintegrasi dengan backend API:

- **Authentication API** - Login, logout, profile
- **Payment API** - Bills, proofs, history
- **Ticket API** - Create, view, manage tickets
- **Profile API** - Update information, statistics

Lihat [API Documentation](../backend/docs/API_DOCUMENTATION.md) untuk detail lengkap.

## 🐛 Troubleshooting

### Common Issues

1. **API Connection Error**
   - Check API URL configuration
   - Verify backend server is running
   - Check CORS settings

2. **Login Issues**
   - Verify username format (HP/PPPoE)
   - Check password (default: 6 digit terakhir HP)
   - Ensure customer account is active

3. **File Upload Issues**
   - Check file size limits
   - Verify file types (jpg, png, pdf)
   - Check network connection

## 📞 Support

Untuk bantuan teknis, hubungi tim development atau buat ticket melalui sistem.