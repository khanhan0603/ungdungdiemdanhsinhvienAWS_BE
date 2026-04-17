// // 🔹 Nếu chọn login user thì vào form login user
// const loginuser = document.getElementById("user-btn");
// if (loginuser) {
//   loginuser.onclick = function () {
//     window.location.href = "{{route('giangvien.login')}}";
//   };
// }

// // 🔹 Nếu chọn login admin thì vào form login admin
// const loginadmin = document.getElementById("admin-btn");
// if (loginadmin) {
//   loginadmin.onclick = function () {
//     window.location.href = "{{route('admin.login')}}";
//   };
// }

// 🔹 Quên mật khẩu
// const quenmatkhau = document.getElementById("forgot-link");
// if (quenmatkhau) {
//   quenmatkhau.onclick = function () {
//     const params = new URLSearchParams(window.location.search);
//     const from = params.get('login') || 'user'||'admin';
//     window.location.href = {{ route('giangvien.forgotPassword') }};
//   };
// }

// // 🔹 Từ form quên mật khẩu → form nhập code
// const sendEmailBtn = document.getElementById("send-email-btn");
// if (sendEmailBtn) {
//   sendEmailBtn.onclick = function () {
//     const params = new URLSearchParams(window.location.search);
//     const from = params.get('login') || 'user'||'admin';
//     window.location.href = `formnhapcode.html?login=${from}`;
//   };
// }

// // 🔹 Từ form nhập code → form đổi mật khẩu
// const verifyBtn = document.getElementById("verify-btn");
// if (verifyBtn) {
//   verifyBtn.onclick = function () {
//     const params = new URLSearchParams(window.location.search);
//     const from = params.get('login') || 'user'||'admin';
//     window.location.href = `formđmk.html?login=${from}`;
//   };
// }

// // 🔹 Thoát ra theo đúng URL (loginadmin hoặc loginuser)
// document.addEventListener('DOMContentLoaded', () => {
//   const thoat = document.getElementById("btn_thoat");
//   const params = new URLSearchParams(window.location.search);
//   const from = params.get('login') || 'user'||'admin';
//   if (thoat) {
//     thoat.onclick = function () {
//       if (from === 'admin') {
//         window.location.href = 'loginadmin.html';
//       } else {
//         window.location.href = 'loginuser.html';
//       }
//     };
//   }
// });

// // 🔹 Quay lại đăng nhập theo đúng URL
// document.addEventListener('DOMContentLoaded', () => {
//   const backLogin = document.getElementById('back-to-login');
//   const params = new URLSearchParams(window.location.search);
//   const from = params.get('login') || 'user';

//   if (backLogin) {
//     backLogin.addEventListener('click', (e) => {
//       e.preventDefault();
//       if (from === 'admin') {
//         window.location.href = 'loginadmin.html';
//       } else {
//         window.location.href = 'loginuser.html';
//       }
//     });
//   }
// });
