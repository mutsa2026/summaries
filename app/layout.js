import 'bootstrap/dist/css/bootstrap.min.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import './globals.css'

export const metadata = {
  title: 'AI Summary Pro',
  description: 'AI-powered text summarization at your fingertips',
}

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body>
        <nav className="navbar navbar-expand-lg navbar-dark" style={{
          background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
          boxShadow: '0 4px 20px rgba(0, 0, 0, 0.1)',
          padding: '1rem 0'
        }}>
          <div className="container">
            <a className="navbar-brand" href="/" style={{
              fontWeight: 700,
              fontSize: '1.5rem',
              display: 'flex',
              alignItems: 'center',
              gap: '0.5rem'
            }}>
              <i className="fas fa-brain"></i>
              AI Summary Pro
            </a>
          </div>
        </nav>

        <div className="container mt-4 mb-5">
          {children}
        </div>

        <div className="spinner-overlay" id="spinnerOverlay" style={{ display: 'none' }}>
          <div className="text-center">
            <div className="custom-spinner"></div>
            <p className="mt-3 fw-bold text-muted">Generating AI Summary...</p>
          </div>
        </div>
      </body>
    </html>
  )
}