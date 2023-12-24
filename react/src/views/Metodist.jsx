import { Link } from "react-router-dom";
import axiosClient from "../axios-client.js";
import { createRef } from "react";
import { useStateContext } from "../context/ContextProvider.jsx";
import { useState, useEffect } from "react";

export default function Login() {
  //   const emailRef = createRef()
  //   const passwordRef = createRef()
  const [user, setMetainfo] = useState([]);
  const [groups, setGroups] = useState([]);
  const [loading, setLoading] = useState(false);
  //   const { setUser, setToken } = useStateContext()
  //   const [message, setMessage] = useState(null)
  useEffect(() => {
    getUsers();
  }, [])
  //   const onSubmit = ev => {
  //     ev.preventDefault()

  // const payload = {
  //   email: emailRef.current.value,
  //   password: passwordRef.current.value,
  // }
  // axiosClient.post('/login', payload)
  //   .then(({data}) => {
  //     setUser(data.user)
  //     setToken(data.token);
  //   })
  //   .catch((err) => {
  //     const response = err.response;
  //     if (response && response.status === 422) {
  //       setMessage(response.data.message)
  //     }
  //   })
  //   }

  const getUsers = () => {
    setLoading(true)
    axiosClient.get(`/metodist?${user.id}`)
      .then(({ data }) => {
        setLoading(false)
        setMetainfo(data.data.user)
        setGroups(data.data.groups)
      })
      .catch(() => {
        setLoading(false)
      })
  }
  return (
    <boby>
      <div>
        <div>Директор</div>
        <div>id: {user.id}, email: {user.email}, fio: {user.fio}</div>
        <br></br>
      </div>
      <div> {groups.map(val => (
        <div>
          <div>Группа: name: {val.short_name}, id: {val.id}, year: {val.year}, number: {val.number}</div>
          <div>{val.subjects.map(vs => (
            <div>
              <div>- Предмет: name: {vs.name}, id: {vs.id}, id_teacher_subject: {vs.id_teacher_subject}</div>
              <div>{val.subjects.map(vs => (
                <div>
                  <div>- Возможный преподаватель: name: {vs.name}, id: {vs.id}, id_teacher_subject: {vs.id_teacher_subject}</div>

                </div>
              ))}</div>
            </div>
          ))}</div>
          <br></br>
        </div>
      ))}</div>
    </boby>
  )
}
{/* <table>
          <thead>
          <tr>
            <th>metainfo</th>
            <th>metodists</th>
          </tr>
          </thead>
          {loading &&
            <tbody>
            <tr>
              <td colSpan="5" class="text-center">
                Loading...
              </td>
            </tr>
            </tbody>
          }
          {!loading &&
            <tbody>
            {users.map(u => (
              <tr key={u.id}>
                <td>{u.metainfo}</td>
                <td>{u.name}</td>
              </tr>
            ))}
            </tbody>
          }
        </table> */}