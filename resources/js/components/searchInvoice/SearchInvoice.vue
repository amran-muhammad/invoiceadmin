<template>
  <div class="max-w-7xl mx-auto" :class="isMobile ? '': 'p-4'">
    <!-- Page Header -->
    <h1 class="text-2xl font-bold mb-4 text-center">Invoices</h1>

    <!-- Search Bar -->
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-2">
      <input
        v-model="search.item_name"
        type="text"
        placeholder="Search by Item Name"
        class="border rounded px-3 py-2 w-full focus:ring focus:ring-blue-300"
      />
      <input
        v-model="search.invoice_number"
        type="text"
        placeholder="Search by Invoice ID"
        class="border rounded px-3 py-2 w-full focus:ring focus:ring-blue-300"
      />
      <input
        v-model="search.mobile"
        type="text"
        placeholder="Search by Mobile"
        class="border rounded px-3 py-2 w-full focus:ring focus:ring-blue-300"
      />
      <input
        v-model="search.client_name"
        type="text"
        placeholder="Search by Customer Name"
        class="border rounded px-3 py-2 w-full focus:ring focus:ring-blue-300"
      />
      <button class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="fetchInvoicesSearch()">Search</button>
      <button class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="clearSearch()">Clear</button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="w-full table-auto text-sm text-left border-collapse">
        <thead class="bg-blue-500 text-white">
          <tr>
            <template v-if="isMobile">
              <th class="px-4 py-2">Invoice ID
                <br>
                 Date
              </th>
              <th class="px-4 py-2">
                Client Name
                <br>
                Mobile
              </th>
            </template>
            <template v-else>
              <th class="px-4 py-2">Invoice ID</th>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Customer Name</th>  
            </template>
            
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(invoice, index) in invoices" :key="index"
            class="hover:bg-gray-100 bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100"
          >
          <template v-if="isMobile">
            <td class="px-4 py-2">{{ invoice.invoice_number }} <br>
            {{ formatDate(invoice.created_at) }} <br>
            </td>
            <td class="px-4 py-2">
              {{ invoice.client_name }}
              <br>
              {{ invoice.mobile }}
            </td>
          </template>
          <template v-else>
            <td class="px-4 py-2">{{ invoice.invoice_number }}</td>
            <td class="px-4 py-2">{{ formatDate(invoice.created_at) }}</td>
            <td class="px-4 py-2">{{ invoice.client_name }}</td>
          </template>
            <td class="px-4 py-2">{{ invoice.total }}</td>
            <td class="px-4 py-2" :class="isMobile ? '': 'flex gap-2'">
              <button
                @click="viewInvoice(invoice)"
                class="bg-purple-500 text-white px-2 py-1 rounded hover:bg-purple-600" :class="isMobile ? 'mb-2': ''"
              >
                View
              </button>
              <button
                @click="editInvoice(invoice, index)"
                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600" :class="isMobile ? 'mb-2': ''"
              >
                Edit
              </button>
              
              <button
                @click="deleteInvoice(invoice.id,index)"
                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <button v-if="invoices.length > 0" class="mt-5 px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="fetchInvoices(nextPage)">Load More</button>
      <!-- Empty State -->
      <div
        v-if="invoices.length === 0"
        class="text-center p-4 text-gray-500"
      >
        No invoices found.
      </div>
    </div>

    
   


  
<div v-if="successModal" id="toast" class="fixed top-5 right-5 bg-white border border-green-500 text-green-700 rounded-lg shadow-lg p-4 transition-transform transform translate-y-full">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/>
            </svg>
            <div>
                <p class="font-bold">Deleted!</p>
                <p>{{ successMessage }}</p>
            </div>
        </div>
    </div>
<div v-if="updateSuccessModal" id="toast" class="fixed top-5 right-5 bg-white border border-green-500 text-green-700 rounded-lg shadow-lg p-4 transition-transform transform translate-y-full">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V7h2v4z"/>
            </svg>
            <div>
                <p class="font-bold">Updated!</p>
                <p>{{ successMessage }}</p>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div v-if="viewModal" id="modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full h-3/4 overflow-hidden">
            <div class="p-6 overflow-y-auto h-full hover:bg-gray-100 bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100">
              <h3><u>Customer Details:</u></h3>
                <h2 class="text-lg font-bold mb-4">{{ viewInvoiceItem.client_name }}</h2>
                <p v-if="viewInvoiceItem.mobile"> {{ viewInvoiceItem.mobile }}</p>
                        <p v-if="viewInvoiceItem.invoice_number"># {{ viewInvoiceItem.invoice_number }}</p>
                        <!-- <p v-if="viewInvoiceItem.address1"> {{ viewInvoiceItem.address1 }}</p> -->
                        <!-- <p v-if="viewInvoiceItem.address2"> {{ viewInvoiceItem.address2 }}</p> -->
                        <p v-if="viewInvoiceItem.created_at">Date: {{ formatDate(viewInvoiceItem.created_at)}}</p>

                        <!-- Bill To -->
                        <div class="bill-to">
                          <h3><u>Shop Details:</u></h3>
                          Owner Shop Name: <input type="text" v-model="shopName"/>
                          <br>
                          <h3>Phone: {{ user.mobile }}</h3>
                        </div>
                        <div class="table-wrapper">
                            <table class="item-table">
                                <thead>
                                    <tr>
                                      <th v-if="!editInvoiceModal">Item</th>
                                      <th v-if="!editInvoiceModal">Description</th>
                                      <th v-if="!editInvoiceModal">Rate</th>
                                      <th v-if="!editInvoiceModal">Quantity</th>
                                        <th v-if="editInvoiceModal">Item Details</th>
                                        <th>Price</th>
                                        <th v-if="editInvoiceModal"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in viewInvoiceItem.items" :key="index">
                                        <td v-if="!editInvoiceModal">
                                          <span>{{ item.name }}</span>
                                        </td>
                                        <td v-if="editInvoiceModal">
                                          <input  placeholder="item name" v-model="item.name"> <br>
                                          <input  placeholder="description" v-model="item.description"> <br>
                                          Rate: <input  placeholder="rate" type="number" step="0.01" v-model.number="item.rate"><br>
                                          Quantity: <input  placeholder="quantity" type="number" min="1" v-model.number="item.quantity">
                                        </td>
                                        <td v-if="!editInvoiceModal">
                                          <span>{{ item.description }}</span>
                                        </td>
                                        <td v-if="!editInvoiceModal">
                                          <span>{{ item.rate }}</span>
                                        </td>
                                        <td v-if="!editInvoiceModal">
                                          <span>{{ item.quantity }}</span>
                                        </td>
                                        <td>{{ formatCurrency(item.rate * item.quantity) }}</td>
                                        <td  v-if="editInvoiceModal"><button @click="removeItem(index)">❌</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button v-if="editInvoiceModal" class="add-btn" @click="addItem()">➕ Add Item</button>
                            </div>
                            <div class="totals">
                <div class="row">
                    <label>Total</label>
                    <input :value="formatCurrency(grandTotal)" readonly>
                </div>
                <!-- Footer -->
                <div class="footer">
                  <input type="text" v-model="thanksText" style="width: 300px;"/>
                <hr>
                  <input type="text" v-model="ownerText"/>
                </div>
            </div>
            <div class="flex justify-end p-4">
                <button v-if="editInvoiceModal" @click="updateInvoice" id="closeModal" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">
                    Update
                </button>
                <button @click="downloadPdf" id="closeModal" class="ml-2 px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">
                    Download Pdf
                </button>
                <button @click="viewModal=false; editInvoiceModal=false;" id="closeModal" class="ml-2 px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">
                    Close
                </button>
            </div>
        </div>
    </div>
    </div>

   


    
  </div>
</template>

<script>
import jsPDF from "jspdf";
import axios from 'axios';
import { format } from 'date-fns'
import { usePage } from '@inertiajs/vue3';

export default {
  data() {
    return {
      page : usePage(),
      thanksText: "THANKS FOR SHOPPING WITH US",
      ownerText: "Made by Samrat Akber",
      shopName: "",
      invoices: [],
      search: {
        invoice_number: '',
        mobile: '',
        client_name: '',
        item_name: '',
        page: 1
      },
      isSearch: false,
      viewModal: false,
      viewInvoiceItem: null,
      currentPage: 0,
      totalPages: 0,
      nextPage: 0,
      successMessage: "",
      successModal: false,
      updateSuccessModal: false,
      isMobile: false,
      editInvoiceModal: false,
      editIndex: -1
    };
  },
    computed: {
    user() {
      return this.page.props.auth.user
    },
     grandTotal() {
      return this.viewInvoiceItem.items.reduce((sum, item) => sum + (item.rate * item.quantity), 0);
    },
  },
  methods: {
    addItem() {
      this.viewInvoiceItem.items.push({ name: "", description: "", rate: 0, quantity: 1 });
    },
    removeItem(index) {
      this.viewInvoiceItem.items.splice(index, 1);
    },
    async fetchInvoicesSearch(){
        this.isSearch = true
        await this.fetchInvoices()
      },
      async clearSearch() {
        this.search = {
          invoice_number: '',
          mobile: '',
          client_name: '',
          page: 1,
          item_name: ''
        }
        this.isSearch = true
      await this.fetchInvoices()
    },
    async fetchInvoices(page = 1) {
      this.search['page']= page
      const res = await axios.get('invoices', { params: this.search });
      if(this.isSearch){
        this.invoices = res.data.data;
        this.isSearch = false
      }
      else{
          this.invoices = this.invoices.concat(res.data.data);
      }
    this.currentPage = res.data.current_page
    this.nextPage = this.currentPage + 1
    this.totalPages = res.data.last_page
    },
    editInvoice(data, index) {
      this.editIndex = index
      this.viewInvoiceItem = data
      this.editInvoiceModal = true
      this.viewModal = true
    },
    async deleteInvoice(id, index) {
      if (confirm('Are you sure?')) {
        await axios.delete(`invoices/${id}`);
        this.successMessage = "Invoice is deleted successfully"
        this.successModal = true
        setTimeout(()=> {
            this.successModal = false
            this.successMessage = ""
        },3000)
        this.invoices.splice(index,1)
        
      }
    },
    formatDate(value) {
      if (!value) return '';
      return format(value,'dd/MM/yyyy HH:mm a')
    },
    viewInvoice(invoice){
        this.viewModal = true
        this.viewInvoiceItem = invoice
    },
    formatCurrency(amount) {
      return `৳${amount}`;
    },
    async downloadPdf() {
      await this.updateInvoice()
      const doc = new jsPDF();
      doc.setFontSize(18);
      doc.text("INVOICE", 90, 15);

      doc.setFontSize(12);
      doc.text("Customer Details:", 14, 30);
      doc.setFont("helvetica", "bold");
      doc.text(this.viewInvoiceItem.client_name, 14, 36);
      doc.setFont("helvetica", "normal");
      // doc.text(this.viewInvoiceItem.address1, 14, 36);
      // doc.text(this.viewInvoiceItem.address2, 14, 42);
      doc.text(this.viewInvoiceItem.mobile, 14, 42);
      doc.text("Shop Details:", 14, 54);
      doc.setFont("helvetica", "bold");
      doc.text(this.shopName, 14, 60);
      doc.text("Phone: "+this.user.mobile, 14, 66);
      doc.setFont("helvetica", "normal");

      doc.text(`Invoice #: ${this.viewInvoiceItem.invoice_number}`, 140, 30);
      doc.text(`Date: ${ this.formatDate(this.viewInvoiceItem.created_at)}`, 140, 36);
      // doc.text(`Amount Due: ${this.viewInvoiceItem.total}`, 140, 42);

      // Table
      let y = 76;
      doc.text("Item", 14, y);
      doc.text("Description", 60, y);
      doc.text("Rate", 110, y);
      doc.text("Qty", 130, y);
      doc.text("Price", 150, y);

      y += 10;
      this.viewInvoiceItem.items.forEach(item => {
        doc.text(item.name ?? "", 14, y);
        doc.text(item.description ?? "", 60, y);
        doc.text(String(item.rate) ?? "", 110, y);
        doc.text(String(item.quantity ?? ""), 130, y);
        doc.text(String(item.rate * item.quantity) ?? "", 150, y);
        y += 10;
      });

      y += 5;
      doc.text(`Total: ${this.grandTotal}`, 14, y);
      y += 6;
      doc.text(`Amount Paid: ${this.grandTotal}`, 14, y);
      y += 6;
      doc.text(`Balance Due: 0.00`, 14, y);

      // Footer
      y += 20;
      doc.setFont("helvetica", "bold");
      doc.text(this.thanksText, 60, y);
      y += 10;
      doc.text(this.ownerText, 14, y);

      doc.save("invoice.pdf");
    },
    async updateInvoice(){
        this.viewModal = false
        this.editInvoiceModal = false
        this.viewInvoiceItem['total'] = this.grandTotal
        const res = await axios.put(`invoices/${this.viewInvoiceItem.id}`,this.viewInvoiceItem);
        if(res){
          this.successMessage = "Invoice updated successfully!"
          this.updateSuccessModal = true
          setTimeout(()=> {
            this.updateSuccessModal = false
            this.successMessage = ""
          },3000) 
        }
        this.invoices[this.editIndex] = {...this.viewInvoiceItem}
        console.log(this.viewInvoiceItem)
    },
  },
  mounted() {
    this.shopName = this.page.props.auth.user.name
    this.fetchInvoices();
    if (window.matchMedia("(max-width: 430px)").matches) {
        this.isMobile = true
      } else {
        this.isMobile = false
      }
  
  }
};
</script>

<style>
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
.bill-to {
  margin: 20px 0;
}
/* Mobile responsive adjustments for table */
@media (max-width: 640px) {
  table {
    font-size: 14px;
  }
  th, td {
    padding: 6px 8px;
  }
}
</style>