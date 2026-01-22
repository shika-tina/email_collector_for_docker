# email_collector_for_docker

A lightweight, production-ready web application built with Docker, featuring a complete LEMP stack (Linux, Nginx, MySQL, PHP) for email collection and management.

## Features

- 🔒 **Password-Protected Admin Panel** - Secure access to email lists
- ✅ **Email Validation** - Automatic format verification
- 🚫 **Duplicate Prevention** - Each email can only be registered once
- 💾 **MySQL Database** - Persistent data storage with Docker volumes
- 🎨 **Modern UI** - Beautiful, responsive interface with gradient designs
- 🐳 **Fully Dockerized** - Easy deployment with Docker Compose
- 🗄️ **Adminer Integration** - Built-in database management interface
- ⚙️ **Environment Configuration** - Customizable via `.env` file

## 📁 Project Structure

```
web/
├── docker-compose.yml      # Docker services configuration
├── Dockerfile              # PHP-FPM custom image
├── nginx.conf              # Nginx server configuration
├── init.sql                # Database initialization script
├── .env.example            # Environment variables template
└── www/                    # Web application files
    ├── index.php           # Homepage with statistics
    ├── email-form-simple.php    # Public email submission form
    └── email-list-simple.php    # Protected email list viewer
```

## 🚀 Quick Start

### Prerequisites

- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose

### Installation

1. **Navigate to the project directory**
   ```bash
   cd web
   ```

2. **Create environment configuration**
   ```bash
   cp .env.example .env
   ```

3. **Customize your settings** (optional)
   
   Edit `.env` file:
   ```env
   # MySQL Database Settings
   DB_HOST=db
   DB_NAME=laratesting2
   DB_USER=root
   DB_PASS=your_secure_password
   
   # Admin Password for Email List
   ADMIN_PASSWORD=your_admin_password
   
   # Application Ports
   APP_PORT=8080
   ADMINER_PORT=8081
   ```

4. **Start the application**
   ```bash
   docker-compose up -d
   ```

5. **Access the application**
   - **Main Application**: http://localhost:8080
   - **Database Manager (Adminer)**: http://localhost:8081

## 📖 Usage

### For Users (Public Access)

1. Visit http://localhost:8080
2. Click "📝 填寫 Email 表單" to submit your email
3. Fill in your email address and submit

### For Administrators (Protected Access)

1. Click "📋 查看 Email 列表" from the homepage
2. Enter the admin password (default: `admin123` - **please change this!**)
3. View all collected emails with timestamps

### Database Management

Access Adminer at http://localhost:8080:8081:
- **System**: MySQL
- **Server**: db
- **Username**: root
- **Password**: (from your `.env` file)
- **Database**: laratesting2

## ⚙️ Configuration

### Changing the Admin Password

**Method 1: Environment Variable (Recommended)**
Edit `.env` file:
```env
ADMIN_PASSWORD=your_new_secure_password
```
Then restart the containers:
```bash
docker-compose restart
```

**Method 2: Direct File Edit**
Edit `www/email-list-simple.php` (line ~12):
```php
$ADMIN_PASSWORD = getenv('ADMIN_PASSWORD') ?: 'your_new_password';
```

### Changing Application Port

Edit `.env` file:
```env
APP_PORT=3000  # Change to your preferred port
```

Restart containers:
```bash
docker-compose up -d
```

### Changing Database Name

1. Edit `.env` file:
   ```env
   DB_NAME=your_database_name
   ```

2. Update `init.sql` if needed

3. Rebuild and restart:
   ```bash
   docker-compose down -v
   docker-compose up -d
   ```

## 🏗️ Architecture

### Services

| Service | Image | Purpose | Port |
|---------|-------|---------|------|
| **web** | nginx:alpine | Web server | 8080 (configurable) |
| **php** | Custom PHP 8.2-FPM | PHP processor | Internal (9000) |
| **db** | mysql:8.0 | Database | Internal (3306) |
| **adminer** | adminer | DB management | 8081 (configurable) |

### Network

All services communicate through a custom bridge network (`app-network`) for isolation and security.

### Volumes

- `mysql_data`: Persistent MySQL data storage
- `./www`: Web application files (bind mount)
- `./nginx.conf`: Nginx configuration (bind mount)

## 🗄️ Database Schema

```sql
CREATE TABLE email_collector (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 🛠️ Development

### Viewing Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f web
docker-compose logs -f php
docker-compose logs -f db
```

### Stopping the Application

```bash
docker-compose down
```

### Stopping and Removing Volumes (⚠️ Data Loss)

```bash
docker-compose down -v
```

### Rebuilding After Changes

```bash
docker-compose up -d --build
```

## 🔒 Security Best Practices

- ✅ Change default admin password immediately
- ✅ Use strong passwords (mix of uppercase, lowercase, numbers, symbols)
- ✅ Keep `.env` file out of version control (already in `.gitignore`)
- ✅ Regularly update Docker images
- ✅ Use HTTPS in production (configure reverse proxy)
- ✅ Implement rate limiting for production use
- ✅ Regular database backups

## 📝 Common Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View running containers
docker-compose ps

# Restart a specific service
docker-compose restart web

# Execute commands in container
docker-compose exec php php -v
docker-compose exec db mysql -u root -p

# View resource usage
docker stats
```

## 🐛 Troubleshooting

### Port Already in Use

```bash
# Change APP_PORT in .env file
APP_PORT=8090

# Restart
docker-compose down
docker-compose up -d
```

### Database Connection Failed

1. Check if database container is running:
   ```bash
   docker-compose ps
   ```

2. Verify environment variables in `.env`

3. Check database logs:
   ```bash
   docker-compose logs db
   ```

### Permission Issues

```bash
# Fix file permissions (Linux/Mac)
sudo chown -R $USER:$USER www/
```

## 📦 Tech Stack

- **Web Server**: Nginx (Alpine)
- **Backend**: PHP 8.2-FPM
- **Database**: MySQL 8.0
- **Container Orchestration**: Docker Compose
- **Database Management**: Adminer

## 📄 License

This project is open source and available for personal and commercial use.

## 🤝 Contributing

Feel free to submit issues and enhancement requests!

---

**Made with ❤️ using Docker**

# 以上的內容均由AI幫我寫的

我的方法

## 安裝(適用linux debian 13 trixie作業系統、aarach64(ARM)架構)

linux查看作業系統<br>
`cat /etc/os-release`

linux查看系統架構<br>
`uname -a`

### 下載並設置docker(不包含安裝 docker desktop，容量過大)

參考 https://linux.how2shout.com/install-docker-debian-13-trixie/

前置作業
```bash
sudo apt update && sudo apt upgrade -y
# Install packages for repository management over HTTPS
sudo apt install ca-certificates curl gnupg lsb-release
# Create the keyrings directory if it doesn’t exist
sudo install -m 0755 -d /etc/apt/keyrings
# Download and add Docker’s GPG key
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
# Add official Docker repository to APT sources on Debian 13.
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
  trixie stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```
```bash
# Install Docker Engine on Debian 13
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```
Add User to Docker Group(Avoid using sudo for every Docker command)
```bash
# Create docker group (if not exists)
sudo groupadd docker
# Add current user to docker group
sudo usermod -aG docker $USER
# Apply group changes
newgrp docker
# then logout and login current user
```
Confirm Docker is working correctly:
```bash
# Check Docker version
docker --version
# Verify daemon is running
sudo systemctl status docker
# Run test container
docker run hello-world
```

## 啟用web server

### docker簡介

docker是一個能夠建立web server的好用工具

### docker架構

docker主要由daemon(守護進程)、client(客戶端)組成<br>
當發送`docker container run`時，我們就是client，daemon則監聽client的請求並管理docker的物件<br>
而物件分成四類: 映像檔(Image)、容器(Container)、虛擬網路(Network)以及Volume

在docker中，文件各自的作用
1. dockerfile 的作用是設定image
2. docker-compose.yml 會告訴 docker daemon怎麼管理image、container和volume還有network
3. init.sql 決定在第一次啟動sql資料庫時，要怎麼初始化資料庫
4. nginx.conf 怎麼設定反向代理器(reverse proxy)
5. www資料夾 裡面放置網頁要呈現的東西，我要連接到後端資料庫，所以是用php語言，而非html,css,js(前端)<br>
不過，這不是唯一的docker資料夾架構，可以千變萬化，例如加入logs紀錄檔目錄、

註: 在我的專案中，sql資料庫是直接存放在volume裡面的，這樣即使關掉伺服器、關掉容器也不會失去資料

```
我的專案:
web/
├── docker-compose.yml      # 容器啟動定義
├── Dockerfile              # 應用程式映像檔定義
├── nginx.conf              # Nginx 虛擬主機設定
├── init.sql                # 資料庫初始化腳本
├── .env                    # 環境變數(存放敏感資料)
├── .htaccess               # 指定哪些檔案可以或禁止訪問
└── www/                    # 網頁應用程式和服務
    ├── index.php           # homepage
    ├── email-form-simple.php
    └── email-list-simple.php
```

### docker好用的地方

docker不用手動安裝例如msql、nginx、php、adminer...，只要將需要的用到的映像寫在docker-coompose.yml，docker就會自動安裝對應的映像，並且不用擔心版本不兼容的問題

### docker指令:

```bash
docker run hello-world   測試

#映像
docker images / docker image ls   檢視所有映像
docker image rm [映像名稱或id] / docker rmi [映像名或id]   刪除映像(需要先刪除使用映像的容器才能刪除映像)
docker image prune    刪除所有旋空的映像
docker build -t [映像名稱]:[版本號]   由dockerfile構建映像
docker pull [映像名稱]   從遠端倉庫拉取映像

#容器
docker ps (-a)   檢視所有容器(以及非運行中的)
docker stop [容器名稱]   停止容器
docker rm [容器名稱]    刪除容器
docker container prune    刪除所有以停止的容器
docker run -d --name [容器自訂名稱] [映像名稱]    新增容器並執行(-d在後台跑)

#系統性
docker system pune -a   刪除所有位使用的鏡像、容器、網路
docker system df   查看docker暫用磁碟情況

#偵錯指令
docker logs -f [容器名稱]    查看server的抱錯
docker exec -it [容器名稱] sh    進入容器內部的終端機(可以按ls或cat)

#開發
docker run -p 8080:80    把電腦的8080洞，接到容器的80洞(在瀏覽器輸入localhost:8080就能看到server畫面)(不能單獨使用，是要設定容器用的)
docker stats    顯示cpu記憶體用量

#例子(但我是直接使用docker-compose.yml來設定並建立容器，而不是用指令)
docker run -d \
  --name my-web-server \
  -p 8080:80 \
  -v $(pwd):/app \
  my-image-name
(-v 把現在資料夾的東西，同步到容器的/app裡面)

#volume
docker volume create [名稱]    建立volume
docker ls    列出所有volume
docker volume insect [名稱]    查看volume詳細資訊(如在電腦上的路徑)
docker volume rm [名稱]    刪除volume

#network
docker network ls    列出所有network
```

### docker compose

講完以上廢話，講到真正如何啟動一個伺服器<br>
在web資料夾中，啟用docker compose，這是一個整合所有上述資料夾下檔案並啟動容器、資料庫、volume、網路的工具<br>
in web/
```bash
docker compose up -d  # 要注意在某些docker版本中docker與compose中間是有"-"的
```

這會使得web資料夾變成一個網頁伺服器，在.env中我們寫到APP_PORT=8080，這使得docker-compose.yml中的nginx伺服器端口是8080，因此在瀏覽器打上
```bash
http://[主機ip]:8080
就可以訪問你的網頁了
```

這時候可以在終端機打上
```bash
docker ps  # 觀察正在使用中的容器
docker ps -a  # 觀察所有容器包括靜止的
docker images  # 所有鏡像
docker network ls  # 所有網路
docker volume ls  # 所有volume
```

接著訪問資料庫<br>
在.env中，設定了ADMINER_PORT=8081, DB_USER=root, DB_PASS=admin123<br>
瀏覽器檢視資料庫
```bash
http://[主機ip]:8081
使用者輸入root、密碼輸入admin123
```

若要關閉伺服器
```bash
docker compose down
```

## 很抱歉我無法將得很詳細>.<

例如如何處理億大堆的問題(資料庫無法連線、yml的設置、不可預測的零零散散的故障...etc)，怎麼更改資料庫名稱、使用者名稱、root密碼(需要進入sql容器的終端並且要更改.env的DB_PASS很麻煩)，若要讓admin資料夾變得不可訪問怎麼做，怎麼使用ngrok分享專案，怎麼自己編寫一個dockerfile、yml、init.sql、nginx.conf，以及docker的完整樣貌....等等的<br>
所以我才讓ai幫我編寫前面的簡介，也許這會讓螢幕前的你更加理解怎麼操作、他的原理;)<br>
你可以直接git clone https://github.com/shika-tina/email_collector_for_docker 並在樹莓派上實做(如果你有的話)，當然如果你是windows那麼就更簡單了，完全不用用這麼亂七八糟的東西，你只要直接使用laragon就好，那會比用docker簡單操作100萬倍