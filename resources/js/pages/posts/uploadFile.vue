<template >
     <form @submit.prevent="submit" enctype="multipart/form-data" >
        <input type="file" id="file" @change="onChange" />
        <input  type="submit" value="Submit" />
    </form>
    
</template>
<style scoped>

</style>
<script>
import axios from 'axios'

export default {
  data() {
    return {
        image:null
    }
},  
methods: {
    onChange(e) {
     // const file = e.target.files[0];
      this.image=e.target.files[0];
      console.log("select file",e.target.files[0]);
    },
    submit() {
        let fed= new FormData();
        fed.append("img",this.image);
      axios.post("/api/posts", fed)
        .then( res => {
            console.log("sucess",res.data);

}).catch(erro=>{
    console.log("fail",erro.data);
});

}
}
}
</script>
