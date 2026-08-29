import os
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        
        # Header (Only on page 2 and later)
        if self._pageNumber > 1:
            self.setFont("Helvetica-Bold", 8)
            self.setFillColor(colors.HexColor("#0284c7"))
            self.drawString(54, 800, "LOEWIX CCTV SURVEILLANCE")
            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748b"))
            self.drawString(180, 800, "|  Product Requirement Document (PRD) & Financial Analysis")
            
            self.setStrokeColor(colors.HexColor("#e2e8f0"))
            self.setLineWidth(0.75)
            self.line(54, 792, 541, 792)

        # Footer (All pages)
        self.setStrokeColor(colors.HexColor("#e2e8f0"))
        self.setLineWidth(0.75)
        self.line(54, 45, 541, 45)
        
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748b"))
        self.drawString(54, 32, "Confidential - PT. LOEWIX INDONESIA (Enterprise Surveillance Suite)")
        
        page_str = f"Halaman {self._pageNumber} dari {page_count}"
        self.drawRightString(541, 32, page_str)
        
        self.restoreState()

def build_pdf():
    pdf_filename = "PRD_Loewix_Billing_Midtrans_Analisis_Finansial.pdf"
    doc = SimpleDocTemplate(
        pdf_filename,
        pagesize=A4,
        leftMargin=40,
        rightMargin=40,
        topMargin=40,
        bottomMargin=40
    )

    styles = getSampleStyleSheet()
    
    # Custom Palette
    c_navy = colors.HexColor("#091650")
    c_blue = colors.HexColor("#0284c7")
    c_dark = colors.HexColor("#0f172a")
    c_muted = colors.HexColor("#475569")
    c_emerald = colors.HexColor("#059669")
    c_bg_light = colors.HexColor("#f8fafc")
    c_border = colors.HexColor("#cbd5e1")
    
    # Custom Typography Styles
    title_style = ParagraphStyle(
        'DocTitle',
        fontName='Helvetica-Bold',
        fontSize=17,
        leading=20,
        textColor=c_navy,
        spaceAfter=2
    )
    
    subtitle_style = ParagraphStyle(
        'DocSubtitle',
        fontName='Helvetica-Bold',
        fontSize=10.5,
        leading=14,
        textColor=c_blue,
        spaceAfter=8
    )
    
    meta_style = ParagraphStyle(
        'DocMeta',
        fontName='Helvetica',
        fontSize=8.5,
        leading=11.5,
        textColor=c_muted
    )

    h1_style = ParagraphStyle(
        'Heading1_Custom',
        fontName='Helvetica-Bold',
        fontSize=11.5,
        leading=14,
        textColor=c_navy,
        spaceBefore=7,
        spaceAfter=4,
        keepWithNext=True
    )
    
    h2_style = ParagraphStyle(
        'Heading2_Custom',
        fontName='Helvetica-Bold',
        fontSize=9.5,
        leading=12.5,
        textColor=c_blue,
        spaceBefore=5,
        spaceAfter=3,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'Body_Custom',
        fontName='Helvetica',
        fontSize=8.5,
        leading=11.5,
        textColor=c_dark,
        spaceAfter=4
    )

    body_bold = ParagraphStyle(
        'Body_Bold',
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11.5,
        textColor=c_dark
    )

    callout_style = ParagraphStyle(
        'CalloutText',
        fontName='Helvetica',
        fontSize=8,
        leading=11,
        textColor=c_dark
    )

    table_header = ParagraphStyle(
        'TableHeader',
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=colors.white,
        alignment=1
    )

    table_cell = ParagraphStyle(
        'TableCell',
        fontName='Helvetica',
        fontSize=7.8,
        leading=10,
        textColor=c_dark
    )
    
    table_cell_center = ParagraphStyle(
        'TableCellCenter',
        fontName='Helvetica',
        fontSize=7.8,
        leading=10,
        textColor=c_dark,
        alignment=1
    )

    table_cell_bold = ParagraphStyle(
        'TableCellBold',
        fontName='Helvetica-Bold',
        fontSize=7.8,
        leading=10,
        textColor=c_dark
    )

    table_cell_green = ParagraphStyle(
        'TableCellGreen',
        fontName='Helvetica-Bold',
        fontSize=7.8,
        leading=10,
        textColor=c_emerald,
        alignment=1
    )

    story = []

    # ==========================================
    # HEADER & DOCUMENT INFO
    # ==========================================
    story.append(Paragraph("PRODUCT REQUIREMENT DOCUMENT (PRD) & FINANCIAL ANALYSIS", title_style))
    story.append(Paragraph("Sistem Monetisasi SaaS CCTV Cloud, Integrasi Payment Gateway Midtrans & Billing Dashboard", subtitle_style))
    
    meta_table_data = [
        [
            Paragraph("<b>Perusahaan:</b> PT. LOEWIX INDONESIA", meta_style),
            Paragraph("<b>Tanggal:</b> 29 Agustus 2026", meta_style),
            Paragraph("<b>Status:</b> Siap Eksekusi (Ready)", meta_style)
        ]
    ]
    meta_table = Table(meta_table_data, colWidths=[180, 160, 175])
    meta_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), c_bg_light),
        ('BOX', (0,0), (-1,-1), 0.75, c_border),
        ('INNERGRID', (0,0), (-1,-1), 0.5, c_border),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(meta_table)
    story.append(Spacer(1, 6))

    # ==========================================
    # 1. EXECUTIVE SUMMARY
    # ==========================================
    story.append(Paragraph("1. Executive Summary & Tujuan Bisnis", h1_style))
    story.append(Paragraph(
        "Dokumen ini memaparkan spesifikasi fungsional dan analisis kelayakan finansial (<i>unit economics</i>) "
        "untuk transformasi portal Loewix CCTV menjadi platform SaaS berlangganan. "
        "Dengan menghubungkan gerbang pembayaran <b>Midtrans</b> dan menyediakan portal manajemen langganan (<i>Billing Hub</i>), "
        "Loewix mengamankan pendapatan berulang bulanan/tahunan (<i>Recurring Revenue</i>) "
        "dengan margin keuntungan bersih lebih dari <b>60%</b>.",
        body_style
    ))

    # ==========================================
    # 2. STRUKTUR PAKET & HARGA
    # ==========================================
    story.append(Paragraph("2. Struktur Paket Langganan & Skema Rekomendasi Harga", h1_style))

    pricing_data = [
        [
            Paragraph("Paket Layanan", table_header),
            Paragraph("Kuota", table_header),
            Paragraph("Harga Bulanan", table_header),
            Paragraph("Harga Tahunan (Hemat 2 Bln)", table_header),
            Paragraph("Fitur Utama", table_header)
        ],
        [
            Paragraph("<b>Starter Cloud</b>", table_cell_bold),
            Paragraph("4 CCTV", table_cell_center),
            Paragraph("Rp 149.000 /bln", table_cell_center),
            Paragraph("Rp 1.490.000 /thn", table_cell_green),
            Paragraph("Full HD 1080p, H.265 Stream, WebRTC/HLS, Cloud Recording 7 Hari", table_cell)
        ],
        [
            Paragraph("<b>Business Pro</b> (Populer)", table_cell_bold),
            Paragraph("10 CCTV", table_cell_center),
            Paragraph("Rp 299.000 /bln", table_cell_center),
            Paragraph("Rp 2.990.000 /thn", table_cell_green),
            Paragraph("2K/4K Ultra HD, AI Motion Detection, Multi-User, Cloud Recording 14 Hari", table_cell)
        ],
        [
            Paragraph("<b>Enterprise Fleet</b>", table_cell_bold),
            Paragraph("20 CCTV", table_cell_center),
            Paragraph("Rp 549.000 /bln", table_cell_center),
            Paragraph("Rp 5.490.000 /thn", table_cell_green),
            Paragraph("4K UHD, AI People/Vehicle Count, Priority Bandwidth, Cloud Recording 30 Hari", table_cell)
        ],
        [
            Paragraph("<b>Corporate Custom</b>", table_cell_bold),
            Paragraph("50 CCTV", table_cell_center),
            Paragraph("Rp 1.199.000 /bln", table_cell_center),
            Paragraph("Rp 11.990.000 /thn", table_cell_green),
            Paragraph("Dedicated Server Relay, Custom Domain & Branding, SLA 99.9% Support", table_cell)
        ]
    ]

    pricing_table = Table(pricing_data, colWidths=[95, 45, 80, 105, 190])
    pricing_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), c_navy),
        ('ALIGN', (0,0), (-1,0), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BOX', (0,0), (-1,-1), 0.75, c_border),
        ('INNERGRID', (0,0), (-1,-1), 0.5, c_border),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, c_bg_light]),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 4),
        ('RIGHTPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(pricing_table)
    story.append(Spacer(1, 6))

    # ==========================================
    # 3. ANALISIS FINANSIAL & UNIT ECONOMICS
    # ==========================================
    story.append(Paragraph("3. Analisis Finansial & Biaya Operasional (JFTech, VPS & Midtrans)", h1_style))
    story.append(Paragraph(
        "• <b>JFTech P2P Gateway API:</b> ~$0.50 - $0.80 USD/kamera/bln (<b>~Rp 8.000 - Rp 10.000 / kamera</b>).<br/>"
        "• <b>Server VPS MediaMTX:</b> 1 unit VPS 1 Gbps (~Rp 250.000/bln) melayani 150-300 kamera (<b>~Rp 2.000 - Rp 3.000 / kamera</b>).<br/>"
        "• <b>Fee Midtrans:</b> QRIS 0.7% atau Virtual Account Flat <b>Rp 4.000 / transaksi</b>.",
        body_style
    ))

    financial_data = [
        [
            Paragraph("Paket", table_header),
            Paragraph("Harga Jual<br/>(Revenue)", table_header),
            Paragraph("Biaya JFTech<br/>(P2P Gateway)", table_header),
            Paragraph("Biaya VPS &<br/>Bandwidth", table_header),
            Paragraph("Fee Midtrans<br/>(Payment)", table_header),
            Paragraph("Total Biaya<br/>(COGS)", table_header),
            Paragraph("Laba Bersih<br/>(Net Profit)", table_header),
            Paragraph("Margin<br/>Profit", table_header)
        ],
        [
            Paragraph("<b>Starter (4 CCTV)</b>", table_cell_bold),
            Paragraph("Rp 149.000", table_cell_center),
            Paragraph("Rp 40.000", table_cell_center),
            Paragraph("Rp 10.000", table_cell_center),
            Paragraph("Rp 4.000", table_cell_center),
            Paragraph("Rp 54.000", table_cell_center),
            Paragraph("<b>Rp 95.000</b>", table_cell_green),
            Paragraph("<b>63.7%</b>", table_cell_green)
        ],
        [
            Paragraph("<b>Pro (10 CCTV)</b>", table_cell_bold),
            Paragraph("Rp 299.000", table_cell_center),
            Paragraph("Rp 90.000", table_cell_center),
            Paragraph("Rp 25.000", table_cell_center),
            Paragraph("Rp 4.000", table_cell_center),
            Paragraph("Rp 119.000", table_cell_center),
            Paragraph("<b>Rp 180.000</b>", table_cell_green),
            Paragraph("<b>60.2%</b>", table_cell_green)
        ],
        [
            Paragraph("<b>Enterprise (20 CCTV)</b>", table_cell_bold),
            Paragraph("Rp 549.000", table_cell_center),
            Paragraph("Rp 160.000", table_cell_center),
            Paragraph("Rp 45.000", table_cell_center),
            Paragraph("Rp 5.000", table_cell_center),
            Paragraph("Rp 210.000", table_cell_center),
            Paragraph("<b>Rp 339.000</b>", table_cell_green),
            Paragraph("<b>61.7%</b>", table_cell_green)
        ]
    ]

    financial_table = Table(financial_data, colWidths=[95, 60, 60, 60, 55, 55, 75, 55])
    financial_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), c_navy),
        ('ALIGN', (0,0), (-1,0), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BOX', (0,0), (-1,-1), 0.75, c_border),
        ('INNERGRID', (0,0), (-1,-1), 0.5, c_border),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, c_bg_light]),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 3),
        ('RIGHTPADDING', (0,0), (-1,-1), 3),
    ]))
    story.append(financial_table)
    story.append(Spacer(1, 5))

    # Highlight box
    box_data = [[
        Paragraph(
            "<b>💡 Kesimpulan Finansial:</b> Biaya operasional 100% tertutup dan menguntungkan. "
            "Paket Tahunan (misal Pro Rp 2.990.000) memberikan <b>Upfront Cashflow</b> yang langsung melunasi biaya server tahunan dari 1 customer pertama dengan <b>Zero Churn Rate</b>.",
            callout_style
        )
    ]]
    box_table = Table(box_data, colWidths=[515])
    box_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f0fdf4")),
        ('BOX', (0,0), (-1,-1), 0.75, colors.HexColor("#86efac")),
        ('TOPPADDING', (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
    ]))
    story.append(box_table)
    
    # PAGE 2
    story.append(PageBreak())

    # ==========================================
    # 4. ARSITEKTUR INTEGRASI PAYMENT MIDTRANS
    # ==========================================
    story.append(Paragraph("4. Arsitektur Integrasi Payment Gateway Midtrans", h1_style))
    story.append(Paragraph(
        "Alur pembayaran menggunakan <b>Midtrans Snap Popup</b> dengan dukungan kanal pembayaran lengkap di Indonesia:",
        body_style
    ))
    
    pay_channels_data = [
        [
            Paragraph("Kategori Kanal", table_header),
            Paragraph("Metode Pembayaran", table_header),
            Paragraph("Waktu Verifikasi", table_header)
        ],
        [
            Paragraph("<b>QRIS & E-Wallet</b>", table_cell_bold),
            Paragraph("GoPay, OVO, Dana, LinkAja, ShopeePay, QRIS BCA/Mandiri", table_cell),
            Paragraph("Instan (< 3 Detik)", table_cell_center)
        ],
        [
            Paragraph("<b>Virtual Account (VA)</b>", table_cell_bold),
            Paragraph("BCA, Bank Mandiri, BRI, BNI, Permata Bank, CIMB Niaga", table_cell),
            Paragraph("Instan Otomatis", table_cell_center)
        ],
        [
            Paragraph("<b>Kartu Kredit / Debit</b>", table_cell_bold),
            Paragraph("Visa, MasterCard, JCB, American Express (3D Secure)", table_cell),
            Paragraph("Instan Real-Time", table_cell_center)
        ]
    ]
    pay_channels_table = Table(pay_channels_data, colWidths=[120, 275, 120])
    pay_channels_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), c_navy),
        ('BOX', (0,0), (-1,-1), 0.75, c_border),
        ('INNERGRID', (0,0), (-1,-1), 0.5, c_border),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, c_bg_light]),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(pay_channels_table)
    story.append(Spacer(1, 5))

    story.append(Paragraph("<b>Alur Proses Transaksi (Workflow):</b>", h2_style))
    story.append(Paragraph("1. <b>Pendaftaran / Checkout:</b> Customer memilih paket di form register atau dashboard billing.<br/>"
                           "2. <b>Generate Snap Token:</b> Backend (<code>api/payment.php</code>) meminta <code>snap_token</code> ke Midtrans API.<br/>"
                           "3. <b>Popup Pembayaran:</b> Frontend membuka pop-up Midtrans Snap di layar tanpa reload halaman.<br/>"
                           "4. <b>Webhook / IPN Callback:</b> Midtrans mengirim notifikasi status terenkripsi (SHA-512 Signature).<br/>"
                           "5. <b>Aktivasi Otomatis:</b> Server memverifikasi pembayaran, mengupdate invoice ke <code>settlement</code>, dan mengaktifkan kuota CCTV.", body_style))
    story.append(Spacer(1, 6))

    # ==========================================
    # 5. SPESIFIKASI MODUL DASHBOARD USER
    # ==========================================
    story.append(Paragraph("5. Spesifikasi Modul Billing di Dashboard User (Customer Hub)", h1_style))

    modules_data = [
        [
            Paragraph("Sub-Menu Dashboard", table_header),
            Paragraph("Fungsi & Fitur yang Ditampilkan", table_header)
        ],
        [
            Paragraph("<b>📦 Informasi Paket</b>", table_cell_bold),
            Paragraph("Nama paket aktif, status masa aktif, kuota CCTV terpakai vs total, tombol Upgrade & Perpanjang Paket.", table_cell)
        ],
        [
            Paragraph("<b>🧾 Informasi Tagihan</b>", table_cell_bold),
            Paragraph("Invoice jatuh tempo / belum terbayar, rincian PPN 11%, dan tombol [ Bayar Sekarang via Midtrans ].", table_cell)
        ],
        [
            Paragraph("<b>💳 Profil Billing</b>", table_cell_bold),
            Paragraph("Nama penagihan resmi, nama perusahaan/PT, email faktur, nomor WhatsApp, alamat tagihan & NPWP.", table_cell)
        ],
        [
            Paragraph("<b>📜 Riwayat Transaksi</b>", table_cell_bold),
            Paragraph("Tabel riwayat pembayaran: Order ID, Tanggal, Paket, Nominal (Rp), Metode, Status (Settlement/Pending), Download Invoice.", table_cell)
        ]
    ]

    modules_table = Table(modules_data, colWidths=[130, 385])
    modules_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), c_navy),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('BOX', (0,0), (-1,-1), 0.75, c_border),
        ('INNERGRID', (0,0), (-1,-1), 0.5, c_border),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, c_bg_light]),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(modules_table)
    story.append(Spacer(1, 8))

    # ==========================================
    # 6. KESIMPULAN & TAHAP EKSEKUSI
    # ==========================================
    story.append(Paragraph("6. Kesimpulan & Pengesahan Dokumen", h1_style))
    story.append(Paragraph(
        "Model bisnis SaaS CCTV Loewix memiliki margin laba bersih <b>~60%</b> dan siap dieksekusi secara terintegrasi.",
        body_style
    ))
    story.append(Spacer(1, 6))

    sign_data = [
        [
            Paragraph("Dipersiapkan oleh:<br/><b>Technical Lead Loewix VMS</b>", meta_style),
            Paragraph("Disetujui oleh:<br/><b>Owner / Management PT. LOEWIX INDONESIA</b>", meta_style)
        ]
    ]
    sign_table = Table(sign_data, colWidths=[255, 260])
    sign_table.setStyle(TableStyle([
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(sign_table)

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Successfully generated clean 2-page PDF: {pdf_filename}")

if __name__ == "__main__":
    build_pdf()

