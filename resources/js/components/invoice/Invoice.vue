<template>
  <div class="invoice-container">
    <!-- Header -->
    <div class="header">
      <h1>INVOICE</h1>
    </div>

    <!-- Info Section -->
    <div class="info-section">
      <div class="from">
        <input type="text" v-model="client_name">
        <br>
        <input type="text" v-model="address1">
        <br>
        <input type="text" v-model="address2">
        <br>
        <input type="text" v-model="mobile">
      </div>
      <div class="to">
        <div class="row">
          <label>Invoice #</label>
          <input v-model="invoiceNumber">
        </div>
        <div class="row">
          <label>Date</label>
          <input v-model="date">
        </div>
        <div class="row">
          <label>Amount Due</label>
          <input :value="formatCurrency(grandTotal)" readonly>
        </div>
      </div>
    </div>

    <!-- Bill To -->
    <div class="bill-to">
        <input type="text" v-model="shopName">
    </div>

    <!-- Item Table -->
    <div class="table-wrapper">
      <table class="item-table">
        <thead>
          <tr>
            <th>Item</th>
            <th>Description</th>
            <th>Rate</th>
            <th>Quantity</th>
            <th>Price</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td><input v-model="item.name"></td>
            <td><input v-model="item.description"></td>
            <td><input type="number" step="0.01" v-model.number="item.rate"></td>
            <td><input type="number" min="1" v-model.number="item.quantity"></td>
            <td>{{ formatCurrency(item.rate * item.quantity) }}</td>
            <td><button @click="removeItem(index)">❌</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <button class="add-btn" @click="addItem">➕ Add Item</button>

    <!-- Totals Section -->
    <div class="totals">
      <div class="row">
        <label>Total</label>
        <input :value="formatCurrency(grandTotal)" readonly>
      </div>
      <div class="row">
        <label>Amount Paid</label>
        <input :value="formatCurrency(amountPaid)">
      </div>
      <div class="row">
        <label>Balance Due</label>
        <input :value="formatCurrency(grandTotal - amountPaid)" readonly>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <input type="text" v-model="thanksText" style="width: 300px;">
      <hr>
        <input type="text" v-model="ownerText">
      
    </div>

    <!-- Download Button -->
    <button class="download-btn" @click="downloadPdf">Download PDF</button>
    <button class="download-btn" @click="saveInvoice">Save</button>
    <div v-if="successModal" id="toast" class="fixed top-5 right-5 bg-white border border-green-500 text-green-700 rounded-lg shadow-lg p-4 transition-transform transform translate-y-full">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/>
            </svg>
            <div>
                <p class="font-bold">Success!</p>
                <p>{{ successMessage }}</p>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import jsPDF from "jspdf";
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

export default {
  data() {
    return {
        page : usePage(),
        thanksText: "THANKS FOR SHOPPING WITH US",
        ownerText: "Made by Samrat Akber",
        shopName: "Test Departmental Store",
        client_name: "Abdur Razzak",
        address1: "101 E. Chapman Ave",
        address2: "Orange, CA 92866",
        mobile: "(800) 555-1234",
        invoiceNumber: this.generateInvoiceNumber(),
        date: new Date().toLocaleDateString(),
        items: [
        { name: "Front End Consultation", description: "Experience Review", rate: 150, quantity: 4 }
        ],
        amountPaid: 0,
        successModal: false,
        successMessage: ""
    };
  },
  computed: {
    grandTotal() {
      return this.items.reduce((sum, item) => sum + (item.rate * item.quantity), 0);
    },
    user() {
      return this.page.props.auth.user
    }
  },
  methods: {
    generateInvoiceNumber() {
        const date = new Date();
        const year = date.getFullYear().toString().slice(-2); // last 2 digits of year
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const random = Math.floor(1000 + Math.random() * 9000); // 4-digit random number
        
        return `INV-${year}${month}${day}-${random}`;
    },
    addItem() {
      this.items.push({ name: "", description: "", rate: 0, quantity: 1 });
    },
    removeItem(index) {
      this.items.splice(index, 1);
    },
    formatCurrency(amount) {
      return `৳${amount.toFixed(2)}`;
    },
    downloadPdf() {
      const doc = new jsPDF();
      doc.setFontSize(18);
      doc.text("INVOICE", 90, 15);

      doc.setFontSize(12);
      doc.setFont("helvetica", "bold");
      doc.text(this.client_name, 14, 30);
      doc.setFont("helvetica", "normal");
      doc.text(this.address1, 14, 36);
      doc.text(this.address2, 14, 42);
      doc.text(this.mobile, 14, 48);
      doc.setFont("helvetica", "bold");
      doc.text(this.shopName, 14, 60);
      doc.setFont("helvetica", "normal");

      doc.text(`Invoice #: ${this.invoiceNumber}`, 140, 30);
      doc.text(`Date: ${this.date}`, 140, 36);
      doc.text(`Amount Due: ${this.grandTotal}`, 140, 42);

      // Table
      let y = 66;
      doc.text("Item", 14, y);
      doc.text("Description", 60, y);
      doc.text("Rate", 110, y);
      doc.text("Qty", 130, y);
      doc.text("Price", 150, y);

      y += 10;
      this.items.forEach(item => {
        doc.text(item.name, 14, y);
        doc.text(item.description, 60, y);
        doc.text(item.rate.toFixed(2), 110, y);
        doc.text(String(item.quantity), 130, y);
        doc.text((item.rate * item.quantity).toFixed(2), 150, y);
        y += 10;
      });

      y += 5;
      doc.text(`Total: ${this.grandTotal}`, 14, y);
      y += 6;
      doc.text(`Amount Paid: ${this.amountPaid}`, 14, y);
      y += 6;
      doc.text(`Balance Due: ${this.grandTotal - this.amountPaid}`, 14, y);

      // Footer
      y += 20;
      doc.setFont("helvetica", "bold");
      doc.text(this.thanksText, 60, y);
      y += 10;
      doc.text(this.ownerText, 14, y);

      doc.save("invoice.pdf");
    },
    async saveInvoice() {
        const invoiceData = {
          invoice_number: this.invoiceNumber,
          client_name: this.client_name,
          items: this.items,
          address1: this.address1,
          address2: this.address2,
          mobile: this.mobile,
          shop_id: this.user.id,
          total: this.grandTotal
        };
  
        try {
          const res = await axios.post('invoices', invoiceData);
          this.invoiceNumber = this.generateInvoiceNumber()
          this.successMessage = res.data.message
          this.successModal = true
          setTimeout(()=> {
            this.successModal = false
            this.successMessage = ""
          },3000)
        } catch (error) {
          console.error(error.response?.data || error.message);
        }
      }
  }
};
</script>

<style>
.invoice-container {
  max-width: 900px;
  margin: auto;
  font-family: Arial, sans-serif;
  padding: 10px;
}
.header {
  background: black;
  color: white;
  text-align: center;
  padding: 10px 0;
}
.info-section {
  display: flex;
  justify-content: space-between;
  margin-top: 15px;
  flex-wrap: wrap;
  gap: 10px;
}
.from, .to {
  width: 45%;
  min-width: 280px;
}
.row {
  display: flex;
  margin-bottom: 5px;
  flex-wrap: wrap;
}
.row label {
  width: 120px;
  font-weight: bold;
}
.row input {
  flex: 1;
  padding: 4px;
  min-width: 120px;
}
.bill-to {
  margin: 20px 0;
}
.table-wrapper {
  overflow-x: auto;
}
.item-table {
  width: 100%;
  border-collapse: collapse;
}
.item-table th, .item-table td {
  border: 1px solid #ddd;
  padding: 6px;
  text-align: left;
}
.item-table input {
  width: 100%;
  padding: 4px;
}
.add-btn {
  margin: 5px 0;
}
.totals {
  margin-top: 15px;
  width: 300px;
  margin-left: auto;
}
.footer {
  text-align: center;
  margin-top: 30px;
}
.footer p {
  font-weight: bold;
  letter-spacing: 3px;
}
.download-btn {
  display: block;
  margin: 20px auto;
  background: green;
  color: white;
  padding: 8px 15px;
  border: none;
  cursor: pointer;
  font-size: 18px;
}

/* Mobile Responsive */
@media (max-width: 600px) {
  .info-section {
    flex-direction: column;
  }
  .from, .to {
    width: 100%;
  }
  .totals {
    width: 100%;
  }
  .row label {
    width: 100%;
    margin-bottom: 3px;
  }
}
</style>
