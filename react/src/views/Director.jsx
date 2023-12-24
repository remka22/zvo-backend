import { Link } from "react-router-dom";
import axiosClient from "../axios-client.js";
import { createRef } from "react";
import { useStateContext } from "../context/ContextProvider.jsx";
import { useState, useEffect } from "react";

export default function Login() {
    //   const emailRef = createRef()
    //   const passwordRef = createRef()
    const [user, setMetainfo] = useState([]);
    const [metodists, setMetodists] = useState([]);
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
        axiosClient.get('/director')
            .then(({ data }) => {
                setLoading(false)
                setMetainfo(data.data.user)
                setMetodists(data.data.metodists)
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
            <div>
                {metodists.map(value => (
                    <div>
                        <div>Методист: id: {value.id}, fio: {value.fio}</div>
                        <div>Группы: {value.groups.map(val => (
                            <div>
                                <div>- name: {val.short_name}, id: {val.id}, year: {val.year}, number: {val.number}</div>
                            </div>
                        ))}</div>
                        <br></br>
                    </div>
                ))}
            </div>
            <div>Все группы: {groups.map(val => (
                <div>
                    <div>- name: {val.short_name}, id: {val.id}, year: {val.year}, number: {val.number}</div>
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