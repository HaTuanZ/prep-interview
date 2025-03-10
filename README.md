# Ngăn Chặn Chia Sẻ Tài Khoản Trong Hệ Thống Thu Phí

## 1. Mô Tả Bài Toán

Trong các hệ thống Web/App có thu phí theo tài khoản, việc chia sẻ tài khoản cho nhiều người sử dụng dẫn đến thiệt hại cho doanh nghiệp. Hệ thống cần có giải pháp phát hiện và ngăn chặn việc lạm dụng chia sẻ tài khoản trong khi vẫn đảm bảo tính trải nghiệm của người dùng.

## 2. Phân tích các hình thức chia sẻ tài khoản phổ biến

Một số các cách thức phổ biến mà có thể được dùng đẻ chia sẻ tài khoản:

### a. Chia sẻ thông tin đăng nhập

- Một người đăng ký tài khoản và chia sẻ email/mật khẩu với nhiều người.
- Những người này có thể sử dụng tài khoản từ nhiều thiết bị, cũng như địa điểm khác nhau.

### b. Đăng nhập từ nhiều thiết bị khác nhau

- Người dùng có thể đăng nhập trên nhiều thiết bị (PC, điện thoại, máy tính bảng).
- Hệ thống không giới hạn số lượng thiết bị sử dụng.

### c. Sử dụng VPN hoặc Proxy để che giấu địa điểm

- Người dùng ở nhiều khu vực khác nhau sử dụng VPN để giả mạo cùng một vị trí.

### d. Tự động chia sẻ phiên đăng nhập

- Người dùng có một số kiến thức về cookie hay token có thể sử dụng chúng chia sẽ cho những người khác.

## 3. Đánh giá ưu nhược điểm của các phương pháp phát hiện và ngăn chặn.

| Phương pháp |	Ưu điểm | Nhược điểm |
| -- | -- | -- |
| Giới hạn số lượng thiết bị | Dễ triển khai, kiểm soát trực tiếp | Người dùng có thể gỡ thiết bị cũ và thêm thiết bị mới liên tục |
| Phát hiện đăng nhập từ nhiều địa chỉ IP/địa lý khác nhau | Hiệu quả khi phát hiện tài khoản bị chia sẻ giữa nhiều quốc gia/khu vực | Người dùng có thể sử dụng VPN để bypass |
| Phân tích hành vi người dùng | Nhận diện chính xác hơn bằng cách theo dõi cách người dùng tương tác với hệ thống | Yêu cầu AI/ML và cần thời gian để thu thập dữ liệu |
| Sử dụng fingerprinting (Nhận dạng trình duyệt/thết bị) | Nhận diện chính xác thiết bị, tránh việc đổi thiết bị liên tục | Một số trình duyệt có thể chặn fingerprint |
| Xác thực 2 bước khi phát hiện bất thường | Hạn chế tài khoản bị dùng bởi nhiều người | Gây bất tiện cho người dùng hợp lệ |

## 4. Đề xuất giải pháp phù hợp

### a. Giới hạn số lượng thiết bị đăng nhập

- Mỗi tài khoản chỉ được đăng nhập trên N thiết bị (cấu hình được).
- Khi người dùng đăng nhập từ thiết bị thứ (N+1), yêu cầu họ xóa một thiết bị cũ.
- Lưu trữ thông tin thiết bị trong CSDL để quản lý.

### b. Xác định vị trí địa lý và địa chỉ IP bất thường

- Nếu phát hiện đăng nhập từ một khu vực khác trong thời gian ngắn → Cảnh báo hoặc yêu cầu xác minh.
- Nếu một tài khoản liên tục đăng nhập từ nhiều địa chỉ IP khác nhau trong thời gian ngắn → Cần xác thực thêm (OTP, captcha).

### c. Phân tích hành vi người dùng

- Ghi lại các hành vi như: Thời gian đăng nhập, thời gian sử dụng, nội dung truy cập.
- Từ đó có thể tích hợp các model AI để xác định hành vi sử dụng không giống với các phiên trước đó → Đánh dấu là bất thường.

### d. Sử dụng Fingerprinting để nhận diện thiết bị

- Thu thập thông tin thiết bị (user-agent, device-infomation,...).
- Nếu phát hiện fingerprint mới mà không phải thiết bị đã lưu trữ → Xác minh lại danh tính người dùng.

### e. Áp dụng xác thực hai bước (2FA) khi phát hiện rủi ro

- Khi phát hiện đăng nhập bất thường (vị trí mới, thiết bị mới, fingerprint mới) → Yêu cầu xác thực qua email/SMS.

## 5. Triển khai Demo

### a. Thiết kế Class diagram

![Class](/diagrams/class.png)

### b. Thiết kế sequence diagram

![Flow](/diagrams/flow.png)

#### a. Người dùng đăng nhập

- Người dùng nhập email và mật khẩu trên giao diện.
- Ứng dụng gửi yêu cầu đăng nhập kèm theo ID thiết bị, fingerprint, và IP.

#### b. Kiểm tra số lượng thiết bị

- Backend truy vấn danh sách thiết bị đang hoạt động của người dùng.
- Nếu vượt quá giới hạn cho phép, yêu cầu đăng nhập bị từ chối.

#### c. Xác minh danh tính

- Nếu số thiết bị hợp lệ, hệ thống tiếp tục xác minh fingerprint & geolocation.
- Nếu phát hiện đăng nhập bất thường, hệ thống yêu cầu MFA (xác thực hai yếu tố).

#### d. Xác thực MFA (nếu cần)

- Nếu mã MFA hợp lệ, phiên đăng nhập được lưu vào database và người dùng có thể truy cập.
- Nếu mã MFA không hợp lệ, yêu cầu bị từ chối.

#### e. Lưu phiên và phân tích hành vi

- Nếu đăng nhập bình thường, hệ thống lưu phiên vào database.
- Hệ thống ghi log hoạt động để phân tích hành vi của người dùng.
- Một job được lên lịch để kiểm tra các phiên bất thường theo thời gian.

## 6. Chạy Ứng dụng

### a. Khởi tạo env

```cmd
cp .env.example .env
```

### b. Chạy ứng dụng

```cmd
docker-compose up -d
```

### c. Chạy migration

```cmd
docker exec -it prep_app php artisan migrate
```

### d. Chạy seed database

```cmd
docker exec -it prep_app php artisan db:seed
```

## 7. Cách thực hiện 

### a. API Đăng nhập

#### **📌 Gửi yêu cầu đăng nhập**
**Method**: `POST`  
**URL**: `http://localhost:8000/api/login`  
**Headers**:  
```json
{
  "Content-Type": "application/json",
  "X-Device-ID": "deviceA",
  "X-Device-info": {"name": "deviceA", "user-agent": "chrome"}
}
```
**Body (JSON - raw)**:  
```json
{
  "email": "test@example.com",
  "password": "12345678"
}
```
**Kết quả mong đợi**:
- ✅ **Nếu hợp lệ** → Trả về **token** (`token`).
- ❌ **Nếu quá số thiết bị** → `"Too many devices"` (403).
- ⚠ **Nếu nghi ngờ chia sẻ tài khoản** → **Yêu cầu nhập MFA**.

---

#### **Xác thực MFA (nếu cần)**
Nếu nhận `"MFA required"`, gửi tiếp yêu cầu:  

**Method**: `POST`
**URL**: `http://localhost:8000/api/verify-mfa`  
**Headers**:  
```json
{
  "Content-Type": "application/json",
  "X-Device-ID": "deviceA",
  "X-Device-info": {"name": "deviceA", "user-agent": "chrome"}
}
```
**Body (JSON - raw)**:  
```json
{
  "email": "test@example.com",
  "mfa_code": "123456"
}
```
**Kết quả mong đợi**:
- ✅ **Nếu mã đúng** → Cho phép đăng nhập.
- ❌ **Nếu sai** → `"Invalid MFA code"` (401).

## 8. Tổng kết

Mặc dù thời gian làm bài test dài, tôi chưa tận dụng được thời gian của mình một cách tối ưu, dẫn đến phần triển khai chưa hoàn thiện đầy đủ. Trong tương lai, nếu có thêm thời gian, tôi sẽ tập trung phát triển các phần sau:

- **Tối ưu thiết kế database**: Chia bảng `device` và `session` riêng để quản lý tốt hơn.
- **Tối ưu query**: Cải thiện hiệu suất truy vấn khi xử lý tập dữ liệu lớn.
- **Cải thiện logic phân tích hành vi**: Thêm logic trong phần job để phân tích hành vi người dùng chi tiết hơn.
- **Bổ sung transaction**: Sử dụng transaction ở những phần quan trọng để đảm bảo tính nhất quán dữ liệu.
- **Cấu trúc lại để sử dụng Redis**: Lưu trữ phiên đăng nhập, giới hạn thiết bị và cache dữ liệu để giảm tải truy vấn vào database.
- **Tích hợp Kafka**: Sử dụng Kafka để xử lý các sự kiện bất đồng bộ như phân tích hành vi, phát hiện gian lận nhằm tối ưu hệ thống khi mở rộng job.

Đây là những điểm cần cải thiện để nâng cao hiệu suất và khả năng mở rộng của hệ thống.
