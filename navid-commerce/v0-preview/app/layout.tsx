import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "نویدشاپ | پیش‌نمایش طراحی v0",
  description: "نمونه تعاملی طراحی فروشگاه فارسی؛ مستقل از پرداخت و داده‌های واقعی ووکامرس",
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return <html lang="fa" dir="rtl"><body>{children}</body></html>;
}
