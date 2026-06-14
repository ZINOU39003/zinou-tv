import type { Metadata } from "next";
import { Cairo } from "next/font/google";
import "./globals.css";
import Navbar from "@/components/layout/Navbar";

const cairo = Cairo({
  subsets: ["arabic", "latin"],
  weight: ["400", "600", "700", "800", "900"],
  variable: "--font-cairo",
});

export const metadata: Metadata = {
  title: "Zinou TV — نتائج المباريات المباشرة",
  description: "تابع نتائج مباريات كرة القدم لحظة بلحظة، ترتيب الدوريات، التشكيلات، والإحصائيات على منصة Zinou TV",
  keywords: "كرة قدم, نتائج مباريات, كأس العالم, دوري أبطال أوروبا, ترتيب, تشكيلة",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ar" dir="rtl">
      <head>
        <link 
          rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          crossOrigin="anonymous" 
          referrerPolicy="no-referrer" 
        />
      </head>
      <body className={`${cairo.variable} font-[family-name:var(--font-cairo)] antialiased`}>
        <Navbar />
        <main className="min-h-screen pt-[70px]">
          {children}
        </main>
      </body>
    </html>
  );
}
