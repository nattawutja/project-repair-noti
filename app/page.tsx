"use client";

import Image from "next/image";
import { FaUser,FaLock,FaEyeSlash,FaEye,FaSignInAlt  } from 'react-icons/fa';
import { useState } from 'react';

export default function Home() {

  const [showPassword, setShowPassword] = useState(false);

   const sendDataLogin = async (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();
      const formData = new FormData(e.currentTarget);
      const username = formData.get("username");
      const password = formData.get("password");
    
        const res = await fetch("http://localhost:8000/dataPage/login.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ username, password }),
        });

        const result = await res.text;
        console.log(result); // เช็คว่า login สำเร็จหรือไม่
    
      // ตัวอย่างส่งข้อมูลไป backend
      //console.log("Login data:", { username, password });
      // fetch('/api/login', { method: 'POST', body: JSON.stringify({ username, password }) }) ...
    };

  return (
    
    <div className="bg-white min-h-screen flex flex-col justify-start items-center gap-4 pt-30 ">
      <label className="font-bold text-black ">
        บริษัท โรงงานผลิตภัณฑ์อาหารไทย จำกัด
      </label>
      
      <Image
        src="/waiwailogo.png"
        alt="My Photo"
        width={200}
        height={50}
      />

      <form
        onSubmit={sendDataLogin}
        className="shadow-xl p-10 border mt-2 rounded-md border-gray-200"
      >
        <div className="flex flex-col items-start w-72">
          <label className="flex items-center gap-2 text-black">
            <FaUser className="text-gray-400" /> 
            ชื่อผู้ใช้งาน
          </label>
          <input
            name="username"
            type="text"
            className="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-black mt-2 w-full"
            placeholder="ชื่อผู้ใช้งาน"
          />
        </div>
    
        <div className="flex flex-col items-start w-72 mt-3">
          <label className="flex items-center gap-2 text-black">
             <FaLock className="text-gray-400" /> 
            รหัสผ่าน
          </label>
          <input
            name="password"
            type="password"
            className="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-black mt-2 w-full"
            placeholder="รหัสผ่าน"
          />
        </div>

        <div className="flex flex-col items-center w-72 mt-5">

          <button type="submit" className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 whitespace-nowrap">
            <FaSignInAlt size={20} />  เข้าสู่ระบบ
          </button>

        </div>

      </form>
      
      
  </div>
  );
}
