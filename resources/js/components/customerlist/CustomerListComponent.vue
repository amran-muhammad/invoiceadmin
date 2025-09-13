<template>
  <div class="hover:bg-gray-100 bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100">
    <div class="invoice-container">
  </div>
   
    <!-- Item Table -->
     <button class="add-btn" @click="addItem">➕ Add Customer</button>
    <div class="table-wrapper">
   
      <table class="item-table">
        <thead>
          <tr>
            <th>Name
            </th>
            <th>Mobile</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td>
              {{ item.name }}
            </td>
            <td>
                {{ item.mobile }}
            </td>
            <td><button @click="removeItem(index)">❌</button>
            <button class="flex items-center space-x-2 p-2 rounded-md" @click="editItem(index)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </button>
            </td>
          </tr>
        </tbody>
      </table>
   
   
    </div>

   
    
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
    <div  v-if="addModal" id="modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full h-3/4 overflow-hidden">
            <div class="p-6 overflow-y-auto h-full hover:bg-gray-100 bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100">
                
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <label for="name">Name</label>
                            <input id="name" type="text" autocomplete="name" v-model="newItem.name" placeholder="Enter name"/>
                        </div>

                    
                        <div class="grid gap-2">
                            <label for="mobile">Mobile Number</label>
                            <input id="mobile" type="textarea" autocomplete="mobile" v-model="newItem.mobile"  placeholder="Enter mobile number"/>
                        </div>
                        

                        <div class="flex justify-end p-4">
                           <button @click="saveProduct" id="closeModal" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">
                               Save
                            </button>
                            <button @click="addModal=false;" id="closeModal" class="ml-2 px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">
                                Close
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

export default {
  data() {
    return {
        page : usePage(),
        items: [],
        newItem: { name: "", mobile: "" },
        search: {
            name: '',
            page: 1
        },
        addModal: false,
        successModal: false,
        successMessage: "",
        isMobile: false,
        item_name_error: false,
        client_name_error: false,
        client_mobile_error: false,
        shop_id: 0,
        editIndex: -1,
        nextPage: 0,
    };
  },
  computed: {
    user() {
      return this.page.props.auth.user
    }
  },
  methods: {
    addItem() {
        this.addModal = true
    },
    async fetchProducts(page) {
      this.search['page']= page
      const res = await axios.get('customerlists', { params: this.search });
      if(page==1){
        this.items = res.data.data;
      }
      else{
          this.items = this.items.concat(res.data.data);
      }
      this.nextPage = res.data.current_page + 1
    },
    async removeItem(id, index){
        if (confirm('Are you sure?')) {
            await axios.delete(`customerlists/${id}`);
            this.successMessage = "Customer is deleted successfully"
            this.successModal = true
            setTimeout(()=> {
                this.successModal = false
                this.successMessage = ""
            },3000)
            this.items.splice(index,1)
        }
    },
    editItem(index) {
        this.addModal = true
        this.editIndex = index
        this.newItem = {...this.items[index]}
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
    
    async saveProduct() {
        if(this.editIndex >-1){
            try {
                this.addModal = false
                const res = await axios.put(`customerlists/${this.items[this.editIndex].id}`,{...this.newItem, shop_id: this.shop_id});
                if(res){
                    this.successMessage = "Customer updated successfully!"
                    this.successModal = true
                    setTimeout(()=> {
                        this.successModal = false
                        this.successMessage = ""
                    },3000) 
                }
                this.items[this.editIndex] = {...this.newItem, shop_id: this.shop_id}
                this.newItem = { name: "", mobile: "", rate: 0 }
                this.editIndex = -1
            } catch (error) {
                
            }
        }
        else{
            try {
                await axios.post('customerlists', {...this.newItem, shop_id: this.shop_id});
                this.successMessage = "Customer added successfully"
                this.successModal = true
                this.items.push({...this.newItem})
                this.newItem = { name: "", mobile: "" }
                this.addModal = false
                setTimeout(()=> {
                this.successModal = false
                this.successMessage = ""
                },3000)
            } catch (error) {
                console.error(error.response?.data || error.message);
            }
        }
    }
  },
  created(){
    this.shop_id = this.page.props.auth.user.id
    this.fetchProducts(1)
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
