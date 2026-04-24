# Railway Deployment Guide

## Prerequisites
- GitHub account (code pushed to a repository)
- Railway account (https://railway.app)
- MongoDB Atlas account (for database)

## Step 1: Set up MongoDB Atlas

1. Go to https://cloud.mongodb.com and create an account
2. Create a new cluster (free tier is fine)
3. Under "Security" → "Network Access", add IP address `0.0.0.0/0` (allow access from anywhere)
4. Under "Security" → "Database Access", create a user with password
5. Get your connection string: Click "Connect" → "Drivers" → Copy the connection string
   - Replace `<password>` with your database user's password
   - Replace `<dbname>` with `cse_reviewer`

## Step 2: Push Code to GitHub

```bash
cd D:\backup\cse_reviewer
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

## Step 3: Deploy on Railway

1. Go to https://railway.app and sign in
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Select your `cse_reviewer` repository
5. Railway will automatically detect and deploy your app

## Step 4: Configure Environment Variables

In your Railway project dashboard:

1. Go to the "Variables" tab
2. Add the following variables:
   - `MONGODB_URI`: Your MongoDB Atlas connection string
   - `NODE_ENV`: `production`
   - `PORT`: Railway will set this automatically

Example `MONGODB_URI`:
```
mongodb+srv://username:password@cluster.mongodb.net/cse_reviewer?retryWrites=true&w=majority
```

## Step 5: Deploy

Railway will automatically:
- Install dependencies for both client and server
- Build the React frontend
- Start the server

## Step 6: Verify Deployment

1. Click on the generated domain in Railway dashboard
2. You should see the CSE Quiz Reviewer app
3. Test the quiz functionality

## Troubleshooting

### Build Fails
- Check the build logs in Railway
- Ensure all dependencies are in package.json
- Verify the build command in railway.json

### Database Connection Error
- Verify MONGODB_URI is correct
- Check MongoDB Atlas IP whitelist includes `0.0.0.0/0`
- Ensure database user has proper permissions

### App Loads but API Calls Fail
- Check that the server is running
- Verify the `/health` endpoint returns `{"status": "ok"}`
- Check Railway logs for errors

## Important Notes

- The frontend is served by the backend in production
- API calls are proxied to the same domain (no CORS issues)
- Railway automatically provides HTTPS

## Project Structure for Railway

```
cse_reviewer/
├── client/          # React frontend (built to client/dist/)
├── server/          # Express backend
├── railway.json     # Railway configuration
└── README.md
```

The server serves the built React app from `client/dist/` in production mode.