# LinkLens AI - Deployment Guide

## 🚀 Quick Deployment Options for Public URL

### Option 1: Railway (Recommended for MVP)
**Fastest & Easiest - 5 minutes setup**

1. **Install Railway CLI:**
```bash
npm install -g @railway/cli
```

2. **Login & Deploy:**
```bash
railway login
railway init
railway up
```

3. **Add Environment Variables in Railway Dashboard:**
- Copy all variables from your `.env` file
- Set `APP_ENV=production`
- Railway will provide MySQL database automatically

**Result:** `https://your-app.railway.app`

---

### Option 2: Vercel (Good for Laravel)
**Free tier with custom domains**

1. **Install Vercel CLI:**
```bash
npm install -g vercel
```

2. **Create `vercel.json`:**
```json
{
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.6.0"
    }
  },
  "routes": [
    { "src": "/(.*)", "dest": "/api/index.php" }
  ]
}
```

3. **Deploy:**
```bash
vercel --prod
```

---

### Option 3: Heroku (Classic Choice)
**Reliable with good Laravel support**

1. **Install Heroku CLI & Login:**
```bash
heroku login
```

2. **Create App:**
```bash
heroku create linklens-ai-mvp
```

3. **Add Buildpack:**
```bash
heroku buildpacks:set heroku/php
```

4. **Deploy:**
```bash
git push heroku main
```

5. **Add Database:**
```bash
heroku addons:create jawsdb:kitefin
```

---

### Option 4: DigitalOcean App Platform
**$5/month with database included**

1. **Connect GitHub repo to DigitalOcean**
2. **Select Laravel preset**
3. **Add environment variables**
4. **Deploy automatically**

---

## 📋 Pre-Deployment Checklist

### 1. **Update Environment for Production:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

### 2. **Create Production Config:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. **Database Migration:**
```bash
php artisan migrate --force
```

### 4. **Queue Worker Setup:**
Most platforms need worker configuration for background jobs.

---

## 🎯 Recommended: Railway Deployment

**Why Railway for MVP:**
- ✅ Zero configuration
- ✅ Automatic HTTPS
- ✅ Built-in database
- ✅ Environment variables UI
- ✅ Free tier available
- ✅ Custom domains

**Steps:**
1. `npm install -g @railway/cli`
2. `railway login`
3. `railway init` (in your project directory)
4. `railway up`
5. Add environment variables in Railway dashboard
6. Your app is live at `https://linklens-ai-production.up.railway.app`

**Time to deploy:** ~5 minutes

---

## 🔧 Post-Deployment Tasks

1. **Update LinkedIn Redirect URI:**
   - Change to your new public URL
   - Update in LinkedIn Developer Console

2. **Test Core Features:**
   - User registration
   - LinkedIn OAuth
   - Post generation
   - Real-time updates

3. **Monitor Logs:**
   - Check for any production errors
   - Verify queue jobs are running

**Your LinkLens AI MVP will be live and ready for testing!** 🎉