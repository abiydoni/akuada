<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\OrderMessageModel;

class DeliveryController extends BaseController
{
    /**
     * Tampilan Khusus Mobile HP Kurir / Driver Vendor
     */
    public function mobileView(string $token)
    {
        $token = trim($token);
        if (empty($token)) {
            return $this->showInvalidToken();
        }

        $orderModel = new OrderModel();
        $order = $orderModel->getOrderByDeliveryToken($token);

        if (!$order) {
            return $this->showInvalidToken();
        }

        if (!empty($order['status_logs'])) {
            $logModel = new OrderStatusLogModel();
            $order['status_logs'] = $logModel->filterLogsForDriver($order['status_logs']);
        }

        return view('delivery/mobile_driver', [
            'title' => 'Kurir Pengantaran PO ' . ($order['po_number'] ?? $order['order_number']) . ' - AkuAda Logistics',
            'order' => $order,
            'token' => $token,
        ]);
    }

    /**
     * Proses Pembaruan Status & Bukti Serah Terima dari HP Kurir
     */
    public function mobileUpdate(string $token)
    {
        $token = trim($token);
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderByDeliveryToken($token);

        if (!$order) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan atau token pengiriman tidak valid.');
            return redirect()->back();
        }

        $stage = trim((string)$this->request->getPost('stage')) ?: 'delivered'; // Alur 1-kali serah terima POD langsung delivered
        $driverName = trim((string)$this->request->getPost('driver_name')) ?: ($order['driver_name'] ?? 'Kurir Armada Vendor');
        $driverPhone = trim((string)$this->request->getPost('driver_phone')) ?: ($order['driver_phone'] ?? '');
        $vehiclePlate = trim((string)$this->request->getPost('vehicle_plate')) ?: ($order['vehicle_plate'] ?? '');
        $recipientName = trim((string)$this->request->getPost('recipient_name'));
        $recipientPhone = trim((string)$this->request->getPost('recipient_phone'));
        $deliveryNotes = trim((string)$this->request->getPost('delivery_notes'));
        $latitude = trim((string)$this->request->getPost('delivery_latitude'));
        $longitude = trim((string)$this->request->getPost('delivery_longitude'));

        $updateData = [
            'delivery_method' => 'vendor_fleet',
            'driver_name'     => $driverName,
            'driver_phone'    => $driverPhone,
            'vehicle_plate'   => $vehiclePlate,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        if (!empty($deliveryNotes)) {
            $updateData['delivery_notes'] = $deliveryNotes;
        }
        if (!empty($recipientName)) {
            $updateData['recipient_name'] = $recipientName;
        }
        if (!empty($recipientPhone)) {
            $updateData['recipient_phone'] = $recipientPhone;
        }
        if (!empty($latitude)) {
            $updateData['delivery_latitude'] = $latitude;
        }
        if (!empty($longitude)) {
            $updateData['delivery_longitude'] = $longitude;
        }

        // Handle Foto Bukti Serah Terima / Pengantaran (Proof of Delivery / POD)
        $photoFile = $this->request->getFile('delivery_proof');
        if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($photoFile->getClientExtension());
            if (in_array($ext, $allowedExts, true)) {
                $targetDir = FCPATH . 'uploads/delivery_proofs/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $safePo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $order['po_number'] ?? ('ord_' . $order['id']));
                $fileName = 'pod_' . $safePo . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $photoFile->move($targetDir, $fileName);
                $updateData['delivery_proof_photo'] = 'uploads/delivery_proofs/' . $fileName;
            }
        }

        $now = date('Y-m-d H:i:s');
        $logTitle = '';
        $logDesc = '';
        $newOrderStatus = $order['order_status'];

        if ($stage === 'delivered') {
            $newOrderStatus = 'delivered';
            $updateData['order_status'] = 'delivered';
            $updateData['delivery_status_detail'] = 'delivered';
            $updateData['delivered_at'] = $now;
            $updateData['delivered_by_name'] = $driverName;

            $logTitle = 'Barang Tiba & Diserahkan di Lokasi Buyer';
            $logDesc = 'Armada vendor (' . $driverName . (!empty($vehiclePlate) ? ' - ' . $vehiclePlate : '') . ') telah menyerahkan barang pesanan di lokasi penerima.'
                     . (!empty($recipientName) ? ' Diterima oleh: ' . $recipientName . '.' : '')
                     . (!empty($deliveryNotes) ? ' Catatan: "' . $deliveryNotes . '"' : '');
            
            // Kirim pesan otomatis ke chat pengadaan
            $msgModel = new OrderMessageModel();
            $msgContent = "📦 **UPDATE PENGIRIMAN DARI KURIR HP:**\n"
                        . "Barang pesanan telah berhasil sampai dan diserahkan di lokasi tujuan oleh driver: **" . $driverName . "**" . (!empty($vehiclePlate) ? " (Plat: " . $vehiclePlate . ")" : "") . ".\n"
                        . (!empty($recipientName) ? "Penerima: **" . $recipientName . "**\n" : "")
                        . (!empty($deliveryNotes) ? "Catatan Kurir: " . $deliveryNotes . "\n" : "")
                        . "Silakan tim Corporate Buyer memeriksa fisik barang dan melanjutkan ke pengesahan BAST.";
            $msgModel->sendMessage(
                (int)$order['id'],
                null,
                $driverName . ' (Kurir Vendor)',
                'vendor',
                $msgContent,
                $order['vendor_name'] ?? 'Vendor'
            );
        } elseif ($stage === 'arrived') {
            if (in_array($order['order_status'], ['po_issued', 'po_approved', 'in_production', 'quality_control', 'packaging'])) {
                $newOrderStatus = 'shipped';
                $updateData['order_status'] = 'shipped';
            }
            $updateData['delivery_status_detail'] = 'arrived';
            $logTitle = 'Kurir Tiba di Titik Serah / Lokasi Buyer';
            $logDesc = 'Driver (' . $driverName . (!empty($vehiclePlate) ? ' - ' . $vehiclePlate : '') . ') telah tiba di lokasi pengiriman buyer. Menunggu pembongkaran/penyerahan barang.'
                     . (!empty($deliveryNotes) ? ' Catatan: "' . $deliveryNotes . '"' : '');
            
            $msgModel = new OrderMessageModel();
            $msgModel->sendMessage(
                (int)$order['id'],
                null,
                $driverName . ' (Kurir Vendor)',
                'vendor',
                "📍 **KURIR TIBA DI LOKASI:** Driver " . $driverName . (!empty($vehiclePlate) ? " (" . $vehiclePlate . ")" : "") . " telah tiba di titik serah buyer. Menunggu proses penurunan/pemeriksaan muatan.",
                $order['vendor_name'] ?? 'Vendor'
            );
        } else {
            // on_the_way (Dalam Perjalanan)
            if (in_array($order['order_status'], ['po_issued', 'po_approved', 'in_production', 'quality_control', 'packaging'])) {
                $newOrderStatus = 'shipped';
                $updateData['order_status'] = 'shipped';
            }
            $updateData['delivery_status_detail'] = 'on_the_way';
            $logTitle = 'Barang Dalam Perjalanan (OTW) oleh Armada Vendor';
            $logDesc = 'Pengiriman pesanan sedang menuju lokasi alamat buyer oleh driver ' . $driverName . (!empty($vehiclePlate) ? ' (No. Plat: ' . $vehiclePlate . ')' : '') . '.'
                     . (!empty($deliveryNotes) ? ' Catatan: "' . $deliveryNotes . '"' : '');
            
            $msgModel = new OrderMessageModel();
            $msgModel->sendMessage(
                (int)$order['id'],
                null,
                $driverName . ' (Kurir Vendor)',
                'vendor',
                "🚚 **BARANG OTW DIKIRIM:** Driver " . $driverName . (!empty($vehiclePlate) ? " (" . $vehiclePlate . ")" : "") . " telah berangkat membawa muatan pesanan menuju alamat perusahaan buyer.",
                $order['vendor_name'] ?? 'Vendor'
            );
        }

        $orderModel->update($order['id'], $updateData);

        // Catat ke log histori status
        $logModel = new OrderStatusLogModel();
        $logModel->logStatus(
            (int)$order['id'],
            $newOrderStatus,
            $logTitle,
            $logDesc,
            $driverName . ' (Kurir HP)'
        );

        session()->setFlashdata('success', 'Status pengiriman berhasil diperbarui menjadi: ' . $logTitle);
        return redirect()->to(base_url('delivery/' . $token));
    }

    private function showInvalidToken()
    {
        return view('delivery/not_found', [
            'title' => 'Link Pengiriman Tidak Ditemukan - AkuAda Logistics'
        ]);
    }
}
