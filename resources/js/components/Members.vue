<template>
	<div>
        <div class="text-center" >
            <button type="button" class="close" @click="hideErr">×</button>
            <h5 v-for = "error in errors">{{error}}</h5>
        </div>
        <div class="row">
            
            <div class="col-md-8">
                <table class="table table-striped">
                   <thead>
                       <tr>
                           <th>#</th> 
                           <th>Name</th> 
                           <th>Information</th> 
                           <th>Phone Number</th> 
                           <th>Action</th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr v-for="member in members" v-bind:key = "member.id">
                            <td>{{member.id}}</td>
                            <td>{{member.name}}</td>
                            <td>{{member.information}}</td>
                            <td>{{member.phone_number}}</td>
                            <td>
                                <button type="button" class="btn btn-danger" @click="deleteMember(member.id)">Delete</button>
                                <button type="button" class="btn btn-warning" @click="editMember(member)">Edit</button>
                            </td>
                       </tr>
                   </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <h1 class="text-center">Add + Edit Form</h1>
                <hr>
                <form @submit.prevent="addMember" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="name" v-model="member.name">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="information" v-model="member.information">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="phone_number" v-model="member.phone_number">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="date_of_birth (yyyy-mm-dd)" v-model="member.date_of_birth">
                    </div>
                    <div>
                        <label>Position</label>
                        <select v-model="member.position">
                            <option value="">select</option>
                            <option value="intern">intern</option>
                            <option value="junior">junior</option>
                            <option value="senior">senior</option>
                            <option value="pm">pm</option>
                            <option value="ceo">ceo</option>
                            <option value="cto">cto</option>
                            <option value="bo">bo</option>
                        </select>
                        <!-- <input type="text" class="form-control" placeholder="position" v-model="member.position"> -->
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select v-model="member.gender">
                            <option value="">select</option>
                            <option value="male">male</option>
                            <option value="female">female</option>
                        </select>
                        <!-- <input type="text" class="form-control" placeholder="gender" v-model="member.gender"> -->
                    </div>
                    <div class="form-group">

                        <input type="file" class="form-control" ref="myImg" placeholder="avatar" @change="onFileChanged">
                        <img v-show="viewed"src="" v-bind:src="avatarSrc" style="width: 15%; height:15%;" class="rounded-circle">
                    </div>
                    <button class="button btn-primary">Submit</button>
                </form>
            </div>

        </div>
        
    </div>
</template>

<script type="text/javascript">
    import axios from 'axios';
    export default{
        data(){
            return {
                addErrors:{},
                errors:[],
                viewed:false,
                avatarSrc:'',
                members: [],
                member: {
                    id: '',
                    name: '',
                    information: '',
                    phone_number: '',
                    date_of_birth: '',
                    position: '',
                    gender: '',
                    avatar: '',
                },
                member_id: '',
                pagination: {},
                edit: false
            }
        },

        created(){
            this.fetchMembers();
        },

        methods: {
            fetchMembers(){
                fetch('/api/members')
                    .then(res => res.json())
                    .then(res => {
                        this.members = res.data;
                    })
            },

            deleteMember(id){
                if(confirm('Are you sure?')){
                    fetch('/api/members/'+id,{
                        method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        if(data.error){
                            this.errors = data;
                            console.log(this.errors);
                        }else{
                            alert('Member removed!');
                            this.fetchMembers();
                        }
                        
                    })
                    .catch(err=>console.log(err));
                }
            },

            onFileChanged () {
              this.member.avatar = event.target.files[0];
              console.log(event.target.files[0]);
            },

            async addMember(){
                if(this.edit === false){
                    //add
                    const formData = new FormData();
                    formData.append('avatar', this.member.avatar)
                    formData.append('name', this.member.name)
                    formData.append('information', this.member.information)
                    formData.append('phone_number', this.member.phone_number)
                    formData.append('date_of_birth', this.member.date_of_birth)
                    formData.append('position', this.member.position)
                    formData.append('gender', this.member.gender)
                    axios.post('http://testapp2.io/api/members',formData )
                    .then(result => {
                        if(result.data.error){
                            this.errors = result.data.error;
                            // console.log(this.addErrors);
                        }else{
                            this.member.id = '';
                            this.member.name = '';
                            this.member.information = '';
                            this.member.phone_number = '';
                            this.member.date_of_birth = '';
                            this.member.position = '';
                            this.member.gender = '';
                            this.member.avatar = '';
                            this.$refs.myImg.value= '';
                            alert('Article added!');
                            this.fetchMembers()
                        }
                      })
                }else{
                    //edit
                    const formData = new FormData();
                    formData.append('avatar', this.member.avatar)
                    formData.append('name', this.member.name)
                    formData.append('information', this.member.information)
                    formData.append('phone_number', this.member.phone_number)
                    formData.append('date_of_birth', this.member.date_of_birth)
                    formData.append('position', this.member.position)
                    formData.append('gender', this.member.gender)
                    formData.append('_method', 'put')
                    axios.post('http://testapp2.io/api/members/'+this.member.id,formData )
                    .then(result => {
                          if(result.data.error){
                              this.errors = result.data.error;
                              // console.log(this.addErrors);
                          }else{
                              this.member.id = '';
                              this.member.name = '';
                              this.member.information = '';
                              this.member.phone_number = '';
                              this.member.date_of_birth = '';
                              this.member.position = '';
                              this.member.gender = '';
                              this.member.avatar = '';
                              this.$refs.myImg.value= '';
                              alert('Member edited!');
                              this.avatarSrc='';
                              this.viewed = false;
                              this.edit = false;
                              this.fetchMembers()
                          }
                      })
                }
            },

            editMember(member){
                this.edit = true;
                this.member.id = member.id;
                this.member.name = member.name;
                this.member.information = member.information;
                this.member.phone_number = member.phone_number;
                this.member.date_of_birth = member.date_of_birth;
                this.member.position = member.position;
                this.member.gender = member.gender;
                this.member.avatar = member.avatar;
                this.avatarSrc="/images/avatars/"+this.member.avatar;
                this.viewed = true;
                this.$refs.myImg.value= '';
            },

            hideErr(){
                this.errors = [];
            }
        }
    }



</script>