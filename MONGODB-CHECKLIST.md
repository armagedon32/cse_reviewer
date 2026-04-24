# MongoDB Checklist

Before running the app, make sure:

1. MongoDB is installed locally.
2. The MongoDB service is running.
3. Port `27017` is open on `127.0.0.1`.
4. The backend can connect to `mongodb://127.0.0.1:27017/cse-reviewer`.

Quick test:

```powershell
Test-NetConnection -ComputerName 127.0.0.1 -Port 27017
```

If the connection fails, start MongoDB first and try again.

# Project Layout

- `cse_reviewer/` is the React frontend
- `cse_reviewer/server/` is the Node + Express backend
- MongoDB stores users and payment records

# About the old Laravel files

The outer repository still shows an older Laravel tree as deleted in git history. The active app now lives inside `cse_reviewer/`. If you want to physically clean up the old Laravel tree, do that only after you confirm you do not need any of those files anymore.
