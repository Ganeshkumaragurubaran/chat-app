<template>
    <div class="welcome-container">
      <div class="welcome-content">
        <h1>Welcome to Our Chat App</h1>
        <p>Join the conversation and connect with others.</p>
        <router-link to="/login" class="btn">Login</router-link>
        <router-link to="/register" class="btn btn-secondary">Register</router-link>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    // Component logic goes here
  };
  </script>
  
  <style scoped>
  /* Add your styling here */
  .welcome-container {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;
  }
  
  .welcome-content {
    text-align: center;
  }
  
  .btn {
    display: inline-block;
    margin: 10px;
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .btn-secondary {
    background-color: #6c757d;
  }
  </style>