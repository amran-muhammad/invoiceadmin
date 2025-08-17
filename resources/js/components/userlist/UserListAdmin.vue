<template>
  <div class="p-4 max-w-7xl mx-auto">
    <!-- Page Header -->
    <h1 class="text-2xl font-bold mb-4 text-center">Invoices</h1>

    <!-- Search Bar -->
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-2">
      <input
        v-model="search.name"
        type="text"
        placeholder="Search by Name"
        class="border rounded px-3 py-2 w-full focus:ring focus:ring-blue-300"
      />
      <input
        v-model="search.email"
        type="text"
        placeholder="Search by Email"
        class="border rounded px-3 py-2 w-full focus:ring focus:ring-blue-300"
      />
      <input
        v-model="search.mobile"
        type="text"
        placeholder="Search by Mobile"
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
           
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Mobile</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(user, index) in user_list" :key="index"
            class="hover:bg-gray-100"
          >
       
            <td class="px-4 py-2">{{ user.name }}</td>
            <td class="px-4 py-2">{{ user.mobile }}</td>
            <td class="px-4 py-2">{{ user.email }}</td>
            <td class="px-4 py-2 flex gap-2">
              <button
                @click="editUser(user)"
                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600"
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

      <button v-if="user_list.length > 0" class="mt-5 px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="fetchInvoices(nextPage)">Load More</button>
      <!-- Empty State -->
      <div
        v-if="user_list.length === 0"
        class="text-center p-4 text-gray-500"
      >
        No user found.
      </div>
    </div>

    
   


  
<div v-if="successModal" id="toast" class="fixed top-5 right-5 bg-white border border-green-500 text-green-700 rounded-lg shadow-lg p-4 transition-transform transform translate-y-full">
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
            <div class="p-6 overflow-y-auto h-full">
                <div class="flex items-center">
                    <label for="status" class="mr-2 text-gray-700">Status:</label>
                        <select
                        id="status"
                        v-model="viewInvoiceItem.status"
                        class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <option value="" disabled>Select status</option>
                        <option value="active" class="bg-white">Active</option>
                        <option value="inactive" class="bg-white">Inactive</option>
                        </select>
                </div>
   
                        
            
                       
            <div class="flex justify-end p-4">
                <button @click="updateUser" id="closeModal" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">
                    Update
                </button>
                <button @click="viewModal=false" id="closeModal" class="ml-2 px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">
                    Close
                </button>
            </div>
        </div>
    </div>
    </div>

   


    
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      user_list: [],
      search: {
        email: '',
        mobile: '',
        name: '',
        status: '',
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
      editIndex: -1
    };
  },
  methods: {
    async fetchInvoicesSearch(){
        this.isSearch = true
        await this.fetchInvoices()
      },
      async clearSearch() {
        this.search = {
          email: '',
          mobile: '',
          name: '',
          page: 1,
        }
        this.isSearch = true
      await this.fetchInvoices()
    },
    async fetchInvoices(page = 1) {
      this.search['page']= page
      const res = await axios.get('userlists', { params: this.search });
      if(this.isSearch){
        this.user_list = res.data.data;
        this.isSearch = false
      }
      else{
          this.user_list = this.user_list.concat(res.data.data);
      }
    this.currentPage = res.data.current_page
    this.nextPage = this.currentPage + 1
    this.totalPages = res.data.last_page
    },
    editInvoice(id) {
      this.$router.push(`invoices/edit/${id}`);
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
        this.user_list.splice(index,1)
        
      }
    },
    async updateUser(){
        this.viewModal = false
        const res = await axios.put(`userlists/${this.viewInvoiceItem.id}`,this.viewInvoiceItem);
        if(res){
          this.successMessage = res.data.message
          this.successModal = true
          setTimeout(()=> {
            this.successModal = false
            this.successMessage = ""
          },3000) 
        }
        console.log(this.viewInvoiceItem)
    },
    editUser(invoice){
        this.viewModal = true
        this.viewInvoiceItem = invoice
    },
  },
  mounted() {
    this.fetchInvoices();
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