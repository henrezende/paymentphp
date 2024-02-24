// import { useState } from "react";
// import reactLogo from "./assets/react.svg";
// import viteLogo from "/vite.svg";
// import "./App.css";
// import { initMercadoPago, getInstallments } from "@mercadopago/sdk-react";

// function App() {
//   const [count, setCount] = useState(0);
//   initMercadoPago("TEST-b1968cca-0d6c-4498-a7c3-854d5901c8f8");

//   const fetchInstallments = async () => {
//     try {
//       const installments = await getInstallments({
//         amount: "10000",
//         locale: "pt-BR",
//         bin: "5031433215406351",
//       });

//       const installments2 = await getInstallments({
//         amount: "32423",
//         bin: "5031433215406351",
//         paymentTypeId: "credit_card",
//       });

//       console.log("Installments:", installments);
//       console.log("Installments2:", installments2);
//     } catch (error) {
//       console.error("Error getting installments:", error);
//     }
//   };

//   return (
//     <>
//       <div>
//         <a href="https://vitejs.dev" target="_blank">
//           <img src={viteLogo} className="logo" alt="Vite logo" />
//         </a>
//         <a href="https://react.dev" target="_blank">
//           <img src={reactLogo} className="logo react" alt="React logo" />
//         </a>
//       </div>
//       <h1> Vite + React </h1>
//       <div className="card">
//         <button onClick={fetchInstallments}>count is {count}</button>
//         <p>
//           Edit <code> src / App.tsx </code> and save to test HMR
//         </p>
//       </div>
//       <p className="read-the-docs">
//         Click on the Vite and React logos to learn more
//       </p>
//     </>
//   );
// }

// export default App;

import React from 'react';
import {
  Route,
  createBrowserRouter,
  createRoutesFromElements,
  RouterProvider,
} from 'react-router-dom';
import Home from './Pages/Home';

const router = createBrowserRouter(
  createRoutesFromElements(
    <Route path="/">
      <Route index element={<Home />} />
    </Route>
  )
);

function App({ routes }) {
  return (
    <>
      <RouterProvider router={router} />
    </>
  );
}

export default App;
