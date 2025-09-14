<template>
  <div class="hover:bg-gray-100 bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100">
    <div class="invoice-container">
  </div>
    <!-- Header -->
    <div class="header">
      <h1>INVOICE</h1>
    </div>

    <!-- Info Section -->
    <div class="info-section">
      <div class="from">
        <u>Customer Details</u><br>
        Name: <input type="text" v-model="client_name" @input="searchCustomers($event, index)">
              <ul v-if="customers.length" class="mt-2 border rounded p-2">
                <li
                  v-for="customer in customers"
                  :key="customer.id"
                  @click="selectCustomer(customer)"
                >
                  {{ customer.name }}
                </li>
              </ul>
        <br>
        Mobile: <input type="text" v-model="mobile">
        <br>
        Address: <input type="text" v-model="address1">
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
        <!-- <div class="row">
          <label>Amount Due</label>
          <input :value="formatCurrency(grandTotal-amountPaid)" readonly>
        </div> -->
      </div>
    </div>

    <!-- Bill To -->
    <div class="bill-to">
    <h3><u>Shop Details:</u></h3>
        Own Shop Name: <input type="text" v-model="shopName">
        <br>
        <h3>Phone: {{ user.mobile }}</h3>
    </div>

    <!-- Item Table -->
    <div class="table-wrapper">
    <template v-if="isMobile">
      <table class="item-table">
        <thead>
          <tr>
            <th>Item
              <br>
            Description</th>
            <th>Rate
              <br>
            Quantity</th>
            <th>Price</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td>
              <input placeholder="item name" v-model="item.name" @input="searchProducts($event, index)">
              <ul v-if="results.length && searchInProductIndex == index" class="mt-2 border rounded p-2">
                <li
                  v-for="product in results"
                  :key="product.id"
                  @click="selectProduct(product,index)"
                >
                  {{ product.name }}
                </li>
              </ul>
               <br>
              <input placeholder="description" v-model="item.description">
            </td>
            <td><input placeholder="rate" type="number" step="0.01" v-model.number="item.rate">
              <br>
            <input placeholder="quantity" type="number" min="1" v-model.number="item.quantity"></td>
            <td>{{ formatCurrency(item.rate * item.quantity) }}</td>
            <td><button @click="removeItem(index)">❌</button></td>
          </tr>
        </tbody>
      </table>
    </template>
    <template v-else>
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
            <td>
              <input placeholder="item name" v-model="item.name" @input="searchProducts($event, index)">
              <ul v-if="results.length && searchInProductIndex == index" class="mt-2 border rounded p-2">
                <li
                  v-for="product in results"
                  :key="product.id"
                  @click="selectProduct(product,index)"
                >
                  {{ product.name }}
                </li>
              </ul>
            </td>
            <td><input placeholder="description" v-model="item.description"></td>
            <td><input placeholder="rate" type="number" step="0.01" v-model.number="item.rate"></td>
            <td><input placeholder="quantity" type="number" min="1" v-model.number="item.quantity"></td>
            <td>{{ formatCurrency(item.rate * item.quantity) }}</td>
            <td><button @click="removeItem(index)">❌</button></td>
          </tr>
        </tbody>
      </table>
    </template>
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
        <input v-model="amountPaid">
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
    <div v-if="client_name_error" id="toast" class="fixed top-5 right-5 bg-white border border-red-500 text-red-700 rounded-lg shadow-lg p-4 transition-transform transform translate-y-full">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/>
            </svg>
            <div>
                <p class="font-bold">Error!</p>
                <p>Please fill up customer name</p>
            </div>
        </div>
    </div>
    <div v-if="client_mobile_error" id="toast" class="fixed top-5 right-5 bg-white border border-red-500 text-red-700 rounded-lg shadow-lg p-4 transition-transform transform translate-y-full">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/>
            </svg>
            <div>
                <p class="font-bold">Error!</p>
                <p>Please fill up customer mobile number</p>
            </div>
        </div>
    </div>
    <div v-if="item_name_error" id="toast" class="fixed top-5 right-5 bg-white border border-red-500 text-red-700 rounded-lg shadow-lg p-4 transition-transform transform translate-y-full">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/>
            </svg>
            <div>
                <p class="font-bold">Error!</p>
                <p>Please flll up item names</p>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import jsPDF from "jspdf";
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';

export default {
  data() {
    return {
        page : usePage(),
        thanksText: "THANKS FOR SHOPPING WITH US",
        ownerText: "Made by Samrat Akber",
        shopName: "",
        client_name: "",
        address1: "",
        // address2: "",
        mobile: "",
        invoiceNumber: this.generateInvoiceNumber(),
        date: format(new Date(),'dd/MM/yyyy'),
        items: [
        { name: "", description: "", rate: 0, quantity: 0 }
        ],
        results: [],
        customers: [],
        amountPaid: 0,
        successModal: false,
        successMessage: "",
        isMobile: false,
        item_name_error: false,
        client_name_error: false,
        client_mobile_error: false,
        searchInProductIndex: -1
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
    checkItem(){
      let flag = false
      for(let item of this.items){
        if(item.name == "" || item.name.trim() == ''){
          flag = true
          break
        }
      }
      return flag
    },
    async downloadPdf() {
      let check = this.checkItem()
      if(this.client_name == '' || this.client_name.trim() == ''){
        this.client_name_error = true
        setTimeout(()=> {
          this.client_name_error = false
        },3000)
        return
      }
      else if(this.mobile == '' || this.mobile.trim() == ''){
        this.client_mobile_error = true
        setTimeout(()=> {
          this.client_mobile_error = false
        },3000)
        return
      }
      if(check){
        this.item_name_error = true
        setTimeout(()=> {
          this.item_name_error = false
        },3000)
        return
      }
      await this.saveInvoice()
      const doc = new jsPDF();
      doc.setFontSize(18);
      doc.text("INVOICE", 90, 15);

      doc.setFontSize(12);
      doc.text("Customer Details", 14, 30);
      doc.setFont("helvetica", "bold");
      doc.text(this.client_name, 14, 36);
      doc.setFont("helvetica", "normal");
      doc.text(this.mobile, 14, 42);
      doc.text(this.address1, 14, 48);
      // doc.text(this.address2, 14, 42);
      doc.text("Shop Details:", 14, 60);
      doc.setFont("helvetica", "bold");
      doc.text(this.shopName, 14, 66);
      doc.text("Phone: "+this.user.mobile, 14, 72);
      doc.setFont("helvetica", "normal");

      doc.text(`Invoice #: ${this.invoiceNumber}`, 140, 30);
      doc.text(`Date: ${this.date}`, 140, 36);
      // doc.text(`Amount Due: ${this.grandTotal}`, 140, 42); 

      // Table
      let y = 82;
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
      let check = this.checkItem()
      if(this.client_name == '' || this.client_name.trim() == ''){
        this.client_name_error = true
        setTimeout(()=> {
          this.client_name_error = false
        },3000)
        return
      }
      else if(this.mobile == '' || this.mobile.trim() == ''){
        this.client_mobile_error = true
        setTimeout(()=> {
          this.client_mobile_error = false
        },3000)
        return
      }
      if(check){
        this.item_name_error = true
        setTimeout(()=> {
          this.item_name_error = false
        },3000)
        return
      }
        const invoiceData = {
          invoice_number: this.invoiceNumber,
          client_name: this.client_name,
          items: this.items,
          address1: this.address1,
          // address2: this.address2,
          mobile: this.mobile,
          shop_id: this.user.id,
          total: this.grandTotal
        };
  
        try {
          const res = await axios.post('invoices', invoiceData);
          this.successMessage = res.data.message
          this.successModal = true
          setTimeout(()=> {
            this.successModal = false
            this.successMessage = ""
          },3000)
        } catch (error) {
          console.error(error.response?.data || error.message);
        }
      },
      searchProducts(event,index){
        this.searchInProductIndex = index
        let query = event.target
        setTimeout(async () => {
          if (query.value.trim().length < 2) {
            this.results = [];
            return;
          }

          try {
            const res = await axios.get(
              `productlists?name=${query.value}`
            );
            this.results = res.data.data; // assuming API returns [{id, name}, ...]
          } catch (err) {
            console.error(err);
          }
        }, 300);
      },
      selectProduct(product, index){
        this.items[index].name = product.name;
        this.items[index].description = product.description;
        this.items[index].rate = product.rate;
        this.results = []
      },
      searchCustomers(event,index){
        this.searchInCustomerIndex = index
        let query = event.target
      
        setTimeout(async () => {
          if (query.value.trim().length < 2) {
            this.results = [];
            return;
          }

          try {
            const res = await axios.get(
              `customerlists?name=${query.value}`
            );
            this.customers = res.data.data; // assuming API returns [{id, name}, ...]
          } catch (err) {
            console.error(err);
          }
        }, 300);
      },
      selectCustomer(customer){
        this.client_name = customer.name;
        this.mobile = customer.mobile;
        this.address1 = customer.address1
        this.customers = []
      }
  },
  created(){
    this.shopName = this.page.props.auth.user.name
    if (window.matchMedia("(max-width: 430px)").matches) {
        this.isMobile = true
      } else {
        this.isMobile = false
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
