# AI Summary Pro

<p align="center">
  <img src="https://img.shields.io/badge/Next.js-14.0-black" alt="Next.js">
  <img src="https://img.shields.io/badge/React-18.0-blue" alt="React">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-purple" alt="Bootstrap">
  <img src="https://img.shields.io/badge/Vercel-Deployed-green" alt="Vercel">
</p>

## About

AI Summary Pro is a modern web application built with Next.js that provides AI-powered text summarization capabilities. Transform long texts into concise, intelligent summaries with advanced features like category detection, word counting, and customizable summary lengths.

## Features

- **AI-Powered Summarization**: Generate intelligent summaries using advanced AI algorithms
- **Category Detection**: Automatically detect and assign categories to your summaries
- **Real-time Statistics**: Track word counts, reading times, and processing metrics
- **Responsive Design**: Beautiful, mobile-friendly interface built with Bootstrap
- **CRUD Operations**: Full create, read, update, and delete functionality
- **Search & Filter**: Advanced filtering and sorting options
- **Regenerate Summaries**: Re-run AI summarization on existing content

## Tech Stack

- **Framework**: Next.js 14 with App Router
- **Frontend**: React 18, Bootstrap 5
- **Styling**: Custom CSS with Font Awesome icons
- **Deployment**: Optimized for Vercel hosting
- **API**: Next.js API Routes for backend functionality

## Getting Started

### Prerequisites

- Node.js 18+
- npm or yarn

### Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   npm install
   ```

3. Run the development server:
   ```bash
   npm run dev
   ```

4. Open [http://localhost:3000](http://localhost:3000) in your browser

### Deployment

This app is optimized for deployment on Vercel:

1. Push your code to GitHub
2. Connect your repository to Vercel
3. Deploy automatically

## Usage

1. **Create Summary**: Click "Create New Summary" to add new content
2. **View Summaries**: Browse your collection with search and filter options
3. **Edit Content**: Modify existing summaries and regenerate AI content
4. **Track Statistics**: Monitor your summarization metrics and progress

## API Routes

- `GET /api/summaries` - List all summaries
- `POST /api/summaries` - Create new summary
- `GET /api/summaries/[id]` - Get specific summary
- `PUT /api/summaries/[id]` - Update summary
- `DELETE /api/summaries/[id]` - Delete summary
- `POST /api/summaries/[id]/regenerate` - Regenerate AI summary

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
