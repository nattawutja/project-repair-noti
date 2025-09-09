"use client";

import { useEffect, useRef } from "react";
import { useRouter } from "next/navigation";

const TIMEOUT_MINUTES = 5;
const TIMEOUT_MS = TIMEOUT_MINUTES * 60 * 1000;

export default function SessionTimeout() {
  const router = useRouter();
  const timeoutRef = useRef<NodeJS.Timeout | null>(null);

  const logout = () => {
    localStorage.clear();
    router.push("/login");
  };

  const resetTimeout = () => {
    if (timeoutRef.current) clearTimeout(timeoutRef.current);
    timeoutRef.current = setTimeout(() => {
      alert("Session หมดอายุ โปรดเข้าสู่ระบบใหม่");
      logout();
    }, TIMEOUT_MS);
  };

  useEffect(() => {
    resetTimeout();
    const events = ["mousemove", "keydown", "click", "scroll", "touchstart"];
    events.forEach((event) => window.addEventListener(event, resetTimeout));

    return () => {
      if (timeoutRef.current) clearTimeout(timeoutRef.current);
      events.forEach((event) => window.removeEventListener(event, resetTimeout));
    };
  }, []);

  return null;
}
