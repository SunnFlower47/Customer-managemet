import React from 'react';

type ErrorBoundaryState = {
  hasError: boolean;
  message?: string;
};

export default class ErrorBoundary extends React.Component<React.PropsWithChildren<{}>, ErrorBoundaryState> {
  constructor(props: React.PropsWithChildren<{}>) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError(error: unknown): ErrorBoundaryState {
    return { hasError: true, message: error instanceof Error ? error.message : 'Unexpected error' };
  }

  componentDidCatch(error: unknown, info: React.ErrorInfo) {
    // Optionally log to monitoring service
    // console.error('ErrorBoundary caught:', error, info);
  }

  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen flex items-center justify-center p-6 bg-gray-50">
          <div className="max-w-md w-full bg-white shadow rounded-lg p-6 border border-gray-200">
            <h1 className="text-lg font-semibold text-gray-900 mb-2">Terjadi Kesalahan</h1>
            <p className="text-sm text-gray-600 mb-4">Maaf, ada kesalahan saat memuat halaman ini.</p>
            <pre className="text-xs text-gray-500 bg-gray-100 p-3 rounded overflow-auto">{this.state.message}</pre>
            <button
              onClick={() => window.location.reload()}
              className="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
            >
              Muat Ulang
            </button>
          </div>
        </div>
      );
    }

    return this.props.children as React.ReactElement;
  }
}


