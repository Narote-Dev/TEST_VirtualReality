# TEST_VirtualReality
# 💱 ระบบแลกเปลี่ยนคริปโต (Crypto Exchange API) - Laravel

โปรเจกต์นี้เป็นระบบ Backend API สำหรับจัดการกระเป๋าเงินดิจิทัล (Crypto Wallet), ธุรกรรมการโอนเงินระหว่างผู้ใช้งาน และระบบจัดการสกุลเงิน  
พัฒนาด้วย Laravel Framework ใช้ระบบการยืนยันตัวตนด้วย Sanctum พร้อมเชื่อมต่อกับฐานข้อมูล MySQL

---

## 🚀 ความสามารถหลักของระบบ

- ✅ สมัคร / ล็อกอิน ผู้ใช้งานด้วย Sanctum
- ✅ จัดการกระเป๋าเงินของแต่ละผู้ใช้ (Wallet)
- ✅ โอนเงินจากผู้ใช้หนึ่งไปยังอีกคน พร้อมหักค่าธรรมเนียม (0.25%)
- ✅ จัดการสกุลเงิน (Currency) ทั้ง Crypto และ Fiat
- ✅ ตรวจสอบยอดเงินของผู้ใช้งานแยกตามสกุลเงิน
- ✅ REST API พร้อมใช้งานกับ Frontend หรือ Postman

---

## ⚙️ ความต้องการของระบบ (System Requirements)

| รายการ         | เวอร์ชันที่แนะนำ |
|----------------|------------------|
| PHP            | >= 8.1           |
| Composer       | ล่าสุด           |
| Laravel        | 10.x             |
| MySQL          | 5.7 / 8.x        |

---

## 📦 ขั้นตอนติดตั้งโปรเจกต์

1. **Clone Project**  
```bash
git clone git clone https://github.com/Narote-Dev/TEST_VirtualReality
```

2. **ติดตั้ง Dependency ด้วย Composer**  
```bash
cd crypto-exchange
composer install
```

3. **ตั้งค่าฐานข้อมูลใน `.env`**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crypto_exchange
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. **สร้าง Application Key**  
```bash
php artisan key:generate
```

5. **รัน Migration เพื่อสร้างตาราง**  
```bash
php artisan migrate
```

6. **รัน Seed ข้อมูลเริ่มต้น**  
```bash
php artisan db:seed
```

7. **เริ่มต้นเซิร์ฟเวอร์ Laravel**  
```bash
php artisan serve
```

---

## 🔐 การยืนยันตัวตน (Authentication)

ระบบใช้ Laravel Sanctum สำหรับ Token-based Authentication

- สมัครผู้ใช้ใหม่: `POST /api/RegisterUser`
- ล็อกอิน: `GET /api/Login`  
  → คืนค่า Token สำหรับใช้งาน API

**หลังจาก Login แล้ว ให้ใส่ Header ต่อไปนี้ในทุก API**  
```
Authorization: Bearer <token>
```

---

## 📮 รายชื่อ API หลัก

### 👤 User
| Method | Endpoint      | คำอธิบาย                      |
|--------|---------------|-------------------------------|
| POST   | /RegisterUser | สมัครผู้ใช้ใหม่               |
| GET    | /Login        | ล็อกอิน                        |
| PUT    | /UpdateProfile         | แก้ไขโปรไฟล์ |
| GET    | /User         | ข้อมูลผู้ใช้งานที่ล็อกอินอยู่ |

### 💼 Wallet
| Method | Endpoint                 | คำอธิบาย                      |
|--------|--------------------------|-------------------------------|
| GET    | /ShowWallet                 | ดูกระเป๋าเงินทั้งหมดของผู้ใช้ |
| GET    | /SelWallet/{currency_id}  | ดูกระเป๋าเงินสกุลเงินนั้น      |
| POST   | /BalanceWallet/{currency_id}  | ดึงยอดเงินของผู้ใช้ที่ล็อกอินอยู่สำหรับสกุลเงินที่ระบุ      |

### 🔄 Transfer
| Method | Endpoint  | คำอธิบาย               |
|--------|-----------|------------------------|
| POST   | /transfer | โอนเงินระหว่างผู้ใช้    |

**ตัวอย่าง Payload:**
```json
{
  "to_user_id": 2,
  "currency_id": 1,
  "amount": 100
}
```

### 💱 Currency
| Method | Endpoint              | คำอธิบาย               |
|--------|-----------------------|------------------------|
| GET    | /AllCurrency           | รายการสกุลเงินทั้งหมด  |
| POST   | /AddCurrency             | เพิ่มสกุลเงินใหม่      |
| PUT    | /UpdateCurrency/{id}  | แก้ไขสกุลเงินตาม ID    |
| GET    | /CheckCurrency/{id}  | ตรวจสอบสกุลเงินที่ระบุ   |
| DELETE    | /DelCurrency/{id}  | ลบสกุลเงิน   |

---

### 🔗 Transaction
| Method | Endpoint              | คำอธิบาย               |
|--------|-----------------------|------------------------|
| GET    | /ShowTransaction           | รายการแลกเปลี่ยนทั้งหมด  |
| GET   | /CheckTransaction/{id}             | ดูรายการแลกเปลี่ยนนั้น      |
| POST    | /AddTransaction  | เพิ่มรายการแลกปเลี่ยนเหรียญ    |


---

## 🧪 การทดสอบ API

แนะนำให้ใช้ Postman หรือ Insomnia ในการส่งคำขอ API

1. สมัครผู้ใช้
2. ล็อกอินเพื่อรับ Token
3. เพิ่ม Header `Authorization: Bearer <token>` ในทุกคำขอ
4. ทดสอบ Endpoints เช่น `/ShowWallet`, `/ShowTransaction`, `/AllCurrency`

---

## 🧾 โครงสร้างโปรเจกต์

```
app/
├── Models/
│   ├── User.php
│   ├── Wallet.php
│   ├── Currency.php
│   └── Transfer.php
├── Http/
│   └── Controllers/
│       ├── AuthController.php
│       ├── WalletController.php
│       ├── CurrencyController.php
│       └── TransferController.php
routes/
└── api.php
```

---

## 🧙‍♂️ ผู้พัฒนา

- ชื่อ: นายนโรตม์ นิลสุขุม  
- GitHub: [https://github.com/Narote-Dev](https://github.com/Narote-Dev)

---

## 📄 License

MIT License © 2025
