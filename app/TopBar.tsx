"use client";

import { usePathname } from "next/navigation";
import { useState,useEffect } from 'react';
import { FaUserCircle  } from 'react-icons/fa';


export default function TopBar() {
  const pathname = usePathname();
  const [fullname, setFullname] = useState("");
  
  useEffect(() => {
    const name = localStorage.getItem("fullname");
    if (name) {
      setFullname(name);
    }
  }, []);

  if (pathname === "/login") {
    return null;
  }
  const handleLogout = () => {
    localStorage.clear();
    window.location.href = "/login";
  };

  return (
    <div
      className="w-full text-black px-6 py-4 flex items-center justify-between shadow-md"
      style={{ backgroundColor: "#fec235" }}
    >
      <div className="space-x-4">
       
      </div>

      <div className="flex items-center justify-end space-x-6">
          <div className="flex items-center gap-1">
            <FaUserCircle size={20} />
            <span> {fullname}</span>
          </div>
          <div className="h-6 border-l border-black-500"></div>

          <button onClick={handleLogout} className="hover:underline cursor-pointer">
            ออกจากระบบ
          </button>
      </div>
    </div>
  );
}
