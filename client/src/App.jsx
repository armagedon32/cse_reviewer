import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import Home from './pages/Home'
import Quiz from './pages/Quiz'
import Results from './pages/Results'
import CreateQuiz from './pages/CreateQuiz'

function App() {
  return (
    <Router>
      <div className="app">
        <header className="header">
          <h1>CSE Quiz Reviewer</h1>
          <nav>
            <a href="/">Home</a>
            <a href="/create">Create Quiz</a>
          </nav>
        </header>
        <main>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/quiz/:id" element={<Quiz />} />
            <Route path="/results" element={<Results />} />
            <Route path="/create" element={<CreateQuiz />} />
          </Routes>
        </main>
      </div>
    </Router>
  )
}

export default App